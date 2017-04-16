<?php
// Routes

/*
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $users = $this->db->table('users')->get();
    print_r($users);

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/

$app->group('/', function () {
  $this->get('', \HomeController::class . ':index');
  $this->get('home', \HomeController::class . ':index');
  $this->get('login', \HomeController::class . ':login')->setName('login');
  $this->get('logout', \HomeController::class . ':logout');
  $this->get('patient', \HomeController::class . ':patient');
  $this->get('medicine', \HomeController::class . ':medicine');
  $this->get('visit', \HomeController::class . ':visit');
  $this->get('dashboard', \HomeController::class . ':dashboard');
});

$app->group('/api', function () {
  $this->post('/login', \UserApi::class . ':login')->setName('post-login');
});

$app->group('/patient', function () {
  $this->get('/lists', \PatientController::class . ':lists')->setName('patient-list');
  $this->get('/add', \PatientController::class . ':add')->setName('patient-add');
  $this->get('/edit', \PatientController::class . ':edit')->setName('patient-edit');
});

$app->group('/medicine', function () {
  $this->get('/lists', \MedicineController::class . ':lists')->setName('medicine-list');
  $this->get('/add', \MedicineController::class . ':add')->setName('medicine-add');
  $this->post('/add', \MedicineApi::class . ':doAdd')->setName('post-medicine-add');
  $this->get('/edit', \MedicineController::class . ':edit')->setName('medicine-edit');
  $this->post('/edit', \MedicineApi::class . ':doEdit')->setName('post-medicine-edit');
});

$app->group('/visit', function () {
  $this->get('/lists', \VisitController::class . ':lists')->setName('visit-list');
  $this->get('/add', \VisitController::class . ':add')->setName('visit-add');
  $this->post('/add', \VisitApi::class . ':doAdd')->setName('post-visit-add');
  $this->get('/edit', \VisitController::class . ':edit')->setName('visit-edit');
  $this->post('/edit', \VisitApi::class . ':doEdit')->setName('post-visit-edit');
});

$app->group('/api/patient', function () {
  $this->get('/lists', \PatientApi::class . ':lists')->setName('api-patient-lists');
  $this->post('/add', \PatientApi::class . ':doAdd')->setName('api-patient-add');
  $this->post('/edit', \PatientApi::class . ':doEdit')->setName('post-api-patient-edit');
  $this->get('/edit', \PatientApi::class . ':edit')->setName('api-patient-edit');
  $this->post('/delete', \PatientApi::class . ':doDelete')->setName('api-patient-delete');
});
