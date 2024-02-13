<?php

namespace App\Classes;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\ExportFile;
use App\Models\FacilityType;
use App\Models\Facilty;
use App\Models\Product;
use League\Csv\Writer;


class DataExportToCsv {

    public function exportFacilities()
    {

        $facilities = Facilty::with(['state', 'type'])->available()->get();

        $facilities = $facilities->map(function ($facility) {
            $facility['facility_type'] = $facility->type?->name ?: 'N/A';
            $facility['facility_state'] = $facility->state?->name ?: 'N/A';
            $facility['facility_statuses'] = $facility->statuses?->pluck('name')->join(', ') ?: 'N/A';
            $facility['facility_contacts'] = $facility->contacts?->pluck('name')->join(', ') ?: 'N/A';
            $facility['facility_branches'] = $facility->branches?->pluck('name')->join(', ') ?: 'N/A';

            unset($facility['id']);
            unset($facility['facility_type_id']);
            unset($facility['type']);
            unset($facility['state']);
            unset($facility['statuses']);
            unset($facility['contacts']);
            unset($facility['branches']);
            unset($facility['facility_contacts']);
            unset($facility['state_id']);
            unset($facility['created_by']);
            unset($facility['updated_by']);
            unset($facility['deleted_by']);

            return $facility;
        });

        // Create a CSV file
        $name = 'facility_' . now()->format('YmdHis');
        $csvFileName = $name . '.csv';
        $csvPath = storage_path('app/exports/' . $csvFileName);

        // Open the CSV file for writing
        $file = fopen($csvPath, 'w');

        // Write headers to the CSV file
        fputcsv($file, array_keys($facilities->first()->toArray()));

        foreach ($facilities as $record) {
            fputcsv($file, $record->toArray());
        }

        // Close the file
        fclose($file);

        ExportFile::create([
            'path' => 'app/exports/' . $csvFileName,
            'name' => $name,
        ]);


        return [
            'path' => $csvPath,
            'file_name' => $csvFileName
        ];
    }

    public function exportContacts()
    {
        $contacts = Contact::available()->get();

        $contacts = $contacts->map(function ($contact) {
            $contact['contact_facility'] = $contact->facilities?->pluck('name')->join(', ') ?: 'N/A';
            $contact['contact_branches'] = $contact->branches?->pluck('name')->join(', ') ?: 'N/A';
            $contact['contact_user'] = $contact->user?->fullName() ?: 'N/A';
            $contact['contact_position'] = $contact->position?->name ?: 'N/A';

            unset($contact['id']);
            unset($contact['facilities']);
            unset($contact['user']);
            unset($contact['position']);

            unset($contact['created_by']);
            unset($contact['updated_by']);
            unset($contact['deleted_by']);

            return $contact;
        });

        // Create a CSV file
        $name = 'contact_' . now()->format('YmdHis');
        $csvFileName = $name . '.csv';
        $csvPath = storage_path('app/exports/' . $csvFileName);

        // Open the CSV file for writing
        $file = fopen($csvPath, 'w');

        // Write headers to the CSV file
        fputcsv($file, array_keys($contacts->first()->toArray()));

        foreach ($contacts as $record) {
            fputcsv($file, $record->toArray());
        }

        // Close the file
        fclose($file);

        ExportFile::create([
            'path' => 'app/exports/' . $csvFileName,
            'name' => $name,
        ]);


        return [
            'path' => $csvPath,
            'file_name' => $csvFileName
        ];
    }

    public function exportProducts()
    {
        $products = Product::available()->get();

        $products = $products->map(function ($product) {


            unset($product['id']);



            unset($product['created_by']);
            unset($product['updated_by']);
            unset($product['deleted_by']);

            return $product;
        });

        // Create a CSV file
        $name = 'product_' . now()->format('YmdHis');
        $csvFileName = $name . '.csv';
        $csvPath = storage_path('app/exports/' . $csvFileName);

        // Open the CSV file for writing
        $file = fopen($csvPath, 'w');

        // Write headers to the CSV file
        fputcsv($file, array_keys($products->first()->toArray()));

        foreach ($products as $record) {
            fputcsv($file, $record->toArray());
        }

        // Close the file
        fclose($file);

        ExportFile::create([
            'path' => 'app/exports/' . $csvFileName,
            'name' => $name,
        ]);


        return [
            'path' => $csvPath,
            'file_name' => $csvFileName
        ];
    }

    public function exportAppointments()
    {
        $appointments = Appointment::all();

        $appointments = $appointments->map(function ($appointment) {

            $appointment['appointment_user'] = $appointment->user?->fullname() ?: 'N/A';
            $appointment['appointment_contact'] = $appointment->contact?->name ?: 'N/A';

            unset($appointment['id']);
            unset($appointment['contact_id']);
            unset($appointment['contact']);
            unset($appointment['user_id']);
            unset($appointment['user']);

            return $appointment;
        });

        // Create a CSV file
        $name = 'appointment_' . now()->format('YmdHis');
        $csvFileName = $name . '.csv';
        $csvPath = storage_path('app/exports/' . $csvFileName);

        // Open the CSV file for writing
        $file = fopen($csvPath, 'w');

        // Write headers to the CSV file
        fputcsv($file, array_keys($appointments->first()->toArray()));

        foreach ($appointments as $record) {
            fputcsv($file, $record->toArray());
        }

        // Close the file
        fclose($file);

        ExportFile::create([
            'path' => 'app/exports/' . $csvFileName,
            'name' => $name,
        ]);


        return [
            'path' => $csvPath,
            'file_name' => $csvFileName
        ];
    }

}

?>
