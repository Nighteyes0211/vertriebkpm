<?php

namespace App\Classes;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\ExportFile;
use App\Models\FacilityType;
use App\Models\Facilty;
use App\Models\Product;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class DataExportToCsv {

    public function exportFacilities()
    {

        $facilities = Facilty::with(['state', 'type'])->available()->get();

        $facilities = $facilities->map(function ($facility) {
            $facility['facility_type'] = $facility->type?->name ?: 'N/A';
            $facility['facility_state'] = $facility->state?->name ?: 'N/A';
            $facility['facility_statuses'] = $facility->statuses?->pluck('name')->join(', ') ?: 'N/A';

            $facility['facility_products'] = null;
            if ($facility->products)
            {
                foreach ($facility->products as $product)
                {
                    $facility['facility_products'] .= $product->product->name . "\n" . $product->quantity . "\n" . '€' . ($product->product->price ?: 0) . "\n" . parseDate($product->created_at) . "\n\n";
                }
            }

            $facility['facility_products'] = $facility['facility_products'] ?: 'N/A';

            $facility['facility_contacts'] = $facility->contacts?->pluck('name')->join(', ') ?: 'N/A';
            $facility['facility_branches'] = $facility->branches?->pluck('name')->join(', ') ?: 'N/A';

            unset($facility['id']);
            unset($facility['facility_type_id']);
            unset($facility['type']);
            unset($facility['state']);
            unset($facility['statuses']);
            unset($facility['contacts']);
            unset($facility['branches']);
            unset($facility['products']);
            unset($facility['facility_contacts']);
            unset($facility['state_id']);
            unset($facility['created_by']);
            unset($facility['updated_by']);
            unset($facility['deleted_by']);

            return $facility;
        });


        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set the column headers
        $spreadsheet->getActiveSheet()->fromArray(
            [
                'Einrichtungsname',
                'Telefon',
                'Straße',
                'Hausnummer',
                'Postleitzahl',
                'Ort',
                'Telefontermin',
                'info_material',
                'Wurde gelöscht',
                'Gelöscht am',
                'Erstellt am',
                'Update am',
                'Intern',
                'E-Mail Adresse',
                'Einrichtungstyp',
                'Bundesland',
                'Einrichtungsstatus',
                'Produkt',
                'Mutterkonzern/Träger',
            ]
            , null, 'A1');

        // Set the data starting from the second row
        $spreadsheet->getActiveSheet()->fromArray($facilities->toArray(), null, 'A2');

        // Create a writer
        $writer = new Xls($spreadsheet);

        // Save the file
        $name = 'einrichtungen_' . now()->format('YmdHis') . '.xls';
        $xlsPath = storage_path('app/exports/' . $name);
        $writer->save($xlsPath);


        ExportFile::create([
            'path' => 'app/exports/' . $name,
            'name' => $name,
        ]);


        return [
            'path' => $xlsPath,
            'file_name' => $name
        ];
    }

    public function exportContacts()
    {
        $contacts = Contact::available()->get();

        $contacts = $contacts->map(function ($contact) {
            $contact['contact_facility'] = $contact->facilities?->pluck('name')->join(', ') ?: 'N/A';
            // $contact['contact_branches'] = $contact->branches?->pluck('name')->join(', ') ?: 'N/A';
            $contact['contact_user'] = $contact->user?->fullName() ?: 'N/A';
            $contact['contact_position'] = $contact->position?->name ?: 'N/A';

            unset($contact['id']);
            unset($contact['facilities']);
            unset($contact['contact_branches']);
            unset($contact['recieve_promotional_emails']);
            unset($contact['branches']);
            unset($contact['user']);
            unset($contact['position']);

            unset($contact['created_by']);
            unset($contact['updated_by']);
            unset($contact['deleted_by']);

            return $contact;
        });

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set the column headers
        $spreadsheet->getActiveSheet()->fromArray(
            [
                'Vorname',
                'Nachname',
                'Telefon',
                'Mobil',
                'Straße',
                'Hausnummer',
                'Postleitzahl',
                'Ort',
                'Benutzer-ID',
                'Status',
                'wurde gelöscht',
                'gelöscht am',
                'erstellt am',
                'update am',
                'Position',
                'Intern',
                'Anrede',
                'E-Mail Adresse',
                'Einrichtung',
                'Zugewiesener Benutzer',
                'Position',
            ], null, 'A1');

        // Set the data starting from the second row
        $spreadsheet->getActiveSheet()->fromArray($contacts->toArray(), null, 'A2');

        // Create a writer
        $writer = new Xls($spreadsheet);

        // Create a CSV file
        $name = 'kontakt_' . now()->format('YmdHis') . '.xls';
        $xlsPath = storage_path('app/exports/' . $name);
        $writer->save($xlsPath);

        ExportFile::create([
            'path' => 'app/exports/' . $name,
            'name' => $name,
        ]);


        return [
            'path' => $xlsPath,
            'file_name' => $name
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

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set the column headers
        $spreadsheet->getActiveSheet()->fromArray([
            'Produktname',
            'Umfang',
            'Unterrichtsart',
            'Preis',
            'Beschreibung',
            'status',
            'wurde gelöscht',
            'gelöscht am',
            'erstellt am',
            'update am',
        ], null, 'A1');

        // Set the data starting from the second row
        $spreadsheet->getActiveSheet()->fromArray($products->toArray(), null, 'A2');

        // Create a writer
        $writer = new Xls($spreadsheet);

        // Create a CSV file
        $name = 'product_' . now()->format('YmdHis') . '.xls';
        $xlsPath = storage_path('app/exports/' . $name);
        $writer->save($xlsPath);

        ExportFile::create([
            'path' => 'app/exports/' . $name,
            'name' => $name,
        ]);


        return [
            'path' => $xlsPath,
            'file_name' => $name
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

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set the column headers
        $spreadsheet->getActiveSheet()->fromArray(array_keys($appointments->first()->toArray()), null, 'A1');

        // Set the data starting from the second row
        $spreadsheet->getActiveSheet()->fromArray($appointments->toArray(), null, 'A2');

        // Create a writer
        $writer = new Xls($spreadsheet);

        // Create a CSV file
        $name = 'appointment_' . now()->format('YmdHis') . '.xls';
        $xlsPath = storage_path('app/exports/' . $name);
        $writer->save($xlsPath);

        ExportFile::create([
            'path' => 'app/exports/' . $name,
            'name' => $name,
        ]);


        return [
            'path' => $xlsPath,
            'file_name' => $name
        ];
    }

}

?>
