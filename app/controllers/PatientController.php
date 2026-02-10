<?php
require_once '../app/models/Patient.php';
require_once '../app/helpers/Response.php';

class PatientController
{
    public function index()
    {
        Response::json(Patient::all());
    }

    public function store()
    {
        Patient::create($GLOBALS['request']['body']);
        Response::success('Patient added', null, 201);
    }

    public function update($id)
    {
        Patient::update($id, $GLOBALS['request']['body']);
        Response::success('Patient updated');
    }

    public function destroy($id)
    {
        Patient::delete($id);
        Response::success('Patient deleted');
    }
}
