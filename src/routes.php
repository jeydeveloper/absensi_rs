<?php
// Routes

$app->group('/', function () {
  $this->get('', \HomeController::class . ':index');
  $this->get('home', \HomeController::class . ':index');
  $this->get('login', \HomeController::class . ':login')->setName('login');
  $this->get('logout', \HomeController::class . ':logout');
});

$app->group('/api', function () {
  $this->post('/login', \UserApi::class . ':login')->setName('post-login');
});

$app->group('/bagian', function () {
  $this->get('', \BagianController::class . ':lists')->setName('bagian-list');
  $this->get('/add', \BagianController::class . ':add')->setName('bagian-add');
  $this->get('/edit', \BagianController::class . ':edit')->setName('bagian-edit');
});

$app->group('/api/bagian', function () {
  $this->get('/lists', \BagianApi::class . ':lists')->setName('api-bagian-lists');
  $this->post('/add', \BagianApi::class . ':doAdd')->setName('post-api-bagian-add');
  $this->post('/edit', \BagianApi::class . ':doEdit')->setName('post-api-bagian-edit');
  $this->get('/edit', \BagianApi::class . ':edit')->setName('api-bagian-edit');
  $this->post('/delete', \BagianApi::class . ':doDelete')->setName('post-api-bagian-delete');
});

$app->group('/status', function () {
  $this->get('', \StatusController::class . ':lists')->setName('status-list');
  $this->get('/add', \StatusController::class . ':add')->setName('status-add');
  $this->get('/edit', \StatusController::class . ':edit')->setName('status-edit');
});

$app->group('/api/status', function () {
  $this->get('/lists', \StatusApi::class . ':lists')->setName('api-status-lists');
  $this->post('/add', \StatusApi::class . ':doAdd')->setName('post-api-status-add');
  $this->post('/edit', \StatusApi::class . ':doEdit')->setName('post-api-status-edit');
  $this->get('/edit', \StatusApi::class . ':edit')->setName('api-status-edit');
  $this->post('/delete', \StatusApi::class . ':doDelete')->setName('post-api-status-delete');
});
