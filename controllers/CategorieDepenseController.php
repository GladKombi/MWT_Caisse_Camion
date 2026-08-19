<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class CategorieDepenseController extends Controller
{
    public function index(): void
    {
        $this->view('categories-depenses/index');
    }
}
