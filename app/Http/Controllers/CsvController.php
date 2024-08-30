<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;

class CsvController extends Controller
{
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv'],
            'tables' => ['required', 'string']
        ]);

        $table = $request->input('tables');
        $path = $request->file('csv_file')?->getRealPath();

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            DB::table($table)->insert($record);
        }

        return redirect()->back()->with('success', 'CSV data imported successfully.');
    }

    public function export(Request $request)
    {
        $request->validate([
            'tables' => 'required|string'
        ]);

        $table = $request->input('tables');
        $data = DB::table($table)->get()->toArray();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(array_keys((array)$data[0]));

        foreach ($data as $row) {
            $csv->insertOne((array)$row);
        }

        $csv->output($table . '_' . time() . '_export.csv');
    }
}
