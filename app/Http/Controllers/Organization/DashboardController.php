<?php

namespace App\Http\Controllers\Organization;

use App\Classes\DataExportToCsv;
use App\Enum\Appointment\StatusEnum;
use App\Enum\RoleEnum;
use App\Http\Controllers\Controller;
use App\Jobs\ImportCsv;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\FacilityType;
use App\Models\Facilty;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $currentAppointment = Appointment::where('user_id', auth()->id())->whereBetween('start_date', [now()->subDay(), now()->addDay()])->count();
        $tomorrowAppointment = Appointment::where('user_id', auth()->id())->whereBetween('start_date', [now()->addDay(), now()->addDays(2)])->count();

        return view('users.organization.dashboard', compact('currentAppointment', 'tomorrowAppointment'));
    }

    public function calendar()
    {
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        $other_users = User::where('is_absent', true)
            ->where(fn ($query) => $query->where('absent_from', '<=', $startDate)->orWhere('absent_to', '>=', $endDate))
            ->where('substitution_handler', auth()->id())
            ->get()
            ->pluck('id')
            ->toArray();
        // $appointments = Appointment::where(fn ($query) => $query->where('user_id', auth()->id())->orWhereIn('user_id', $other_users))->get();
        $appointments = Appointment::all();

        // id: '1',
        // start: curYear + '-' + curMonth + '-02T09:00:00',
        // end: curYear + '-' + curMonth + '-02T13:00:00',
        // title: 'Spruko Meetup',
        // backgroundColor: 'rgba(71, 84, 242, 0.2)',
        // borderColor: 'rgba(71, 84, 242, 0.2)',
        // description: 'All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary'
        $appointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'appointment_edit_link' => route('organization.appointment.edit', $appointment->id),
                'appointment_delete_link' => route('organization.appointment.delete', $appointment->id),
                'title' => $appointment->name,
                'start' => $appointment->start_date,
                'end' => $appointment->end_date,
                'contact' => $appointment->contact?->fullName() ?: 'N/A',
                'contact_link' => $appointment->contact ? route('organization.contact.edit', $appointment->contact?->id) : '#',
                'position' => $appointment->contact?->position?->name ?: 'N/A',
                'phone_number' => $appointment->contact?->mobile ?: 'N/A',
                'facilities' => $appointment->contact?->facilities->pluck('name')->join(', ') ?: 'N/A',
                'status' => StatusEnum::tryFrom($appointment->status)->label(),
                'status_class' => $appointment->status == StatusEnum::PENDING->value ? 'badge bg-warning' : 'badge bg-success',
                'appointment_start_time' => parseDate($appointment->start_date, 'M j, Y h:i A'),
                'appointment_end_time' => parseDate($appointment->end_date, 'M j, Y h:i A'),
                'user' => $appointment->user->fullName()
            ];
        });

        return view('users.organization.calendar', compact('appointments'));
    }

    public function importData()
    {

        $data = [];
        // Check if the file exists
        if (file_exists(storage_path('app/data.csv'))) {
            // Open the CSV file for reading
            $file = fopen(storage_path('app/data.csv'), 'r');


            // Skip header
            $row_index = -1;

            // Read and parse each row
            while (($row = fgetcsv($file)) !== false) {


                $row_index += 1;
                if ($row_index == 0 && true) {
                    continue;
                }


                $data[] = $row;
            }

            // Close the file
            fclose($file);
        }

        $data = array_chunk($data, 50);
        $readyData = [];
        $batches = Bus::batch([])->dispatch();
        foreach ($data as $chunk)
        {
            // $readyData[] = new ImportCsv($chunk);
            $batches->add(new ImportCsv($chunk));
        }

        return redirect()->back()->with('success', 'Data is being imported.');

    }

    public function exportData(Request $request)
    {

        $exportData = new DataExportToCsv();

        if ($request->data == 'facility')
        {
            $response = $exportData->exportFacilities();
            return response()->download($response['path'], $response['file_name']);
        } elseif($request->data == 'contact')
        {
            $response = $exportData->exportContacts();
            return response()->download($response['path'], $response['file_name']);
        } elseif($request->data == 'product')
        {
            $response = $exportData->exportProducts();
            return response()->download($response['path'], $response['file_name']);
        } elseif($request->data == 'appointment')
        {
            $response = $exportData->exportAppointments();
            return response()->download($response['path'], $response['file_name']);
        }

        return redirect()->back()->with('error', 'Something went wrong.');

    }

    public function export()
    {
        if (!auth()->user()->hasRole(RoleEnum::SUPERADMINISTRATOR->value))
        {
            return abort(404);
        }

        return view('users.organization.export');

    }
}
