<?php
// Routes

$app->group('/', function () {
  $this->get('', \BagianController::class . ':lists')->setName('bagian-list');
  $this->get('home', \HomeController::class . ':index');
  $this->get('login', \HomeController::class . ':login')->setName('login');
  $this->get('change-password', \HomeController::class . ':changePassword')->setName('change-password');
  $this->get('logout', \HomeController::class . ':logout')->setName('logout');
});

$app->group('/api', function () {
  $this->post('/login', \UserApi::class . ':login')->setName('post-login');
    $this->post('/change-password', \UserApi::class . ':changePassword')->setName('post-change-password');
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

$app->group('/jabatan', function () {
  $this->get('', \JabatanController::class . ':lists')->setName('jabatan-list');
  $this->get('/add', \JabatanController::class . ':add')->setName('jabatan-add');
  $this->get('/edit', \JabatanController::class . ':edit')->setName('jabatan-edit');
});

$app->group('/api/jabatan', function () {
  $this->get('/lists', \JabatanApi::class . ':lists')->setName('api-jabatan-lists');
  $this->post('/add', \JabatanApi::class . ':doAdd')->setName('post-api-jabatan-add');
  $this->post('/edit', \JabatanApi::class . ':doEdit')->setName('post-api-jabatan-edit');
  $this->get('/edit', \JabatanApi::class . ':edit')->setName('api-jabatan-edit');
  $this->post('/delete', \JabatanApi::class . ':doDelete')->setName('post-api-jabatan-delete');
});

$app->group('/unit', function () {
  $this->get('', \UnitController::class . ':lists')->setName('unit-list');
  $this->get('/add', \UnitController::class . ':add')->setName('unit-add');
  $this->get('/edit', \UnitController::class . ':edit')->setName('unit-edit');
});

$app->group('/api/unit', function () {
  $this->get('/lists', \UnitApi::class . ':lists')->setName('api-unit-lists');
  $this->post('/add', \UnitApi::class . ':doAdd')->setName('post-api-unit-add');
  $this->post('/edit', \UnitApi::class . ':doEdit')->setName('post-api-unit-edit');
  $this->get('/edit', \UnitApi::class . ':edit')->setName('api-unit-edit');
  $this->post('/delete', \UnitApi::class . ':doDelete')->setName('post-api-unit-delete');
});

$app->group('/employee', function () {
  $this->get('', \EmployeeController::class . ':lists')->setName('employee-list');
  $this->get('/add', \EmployeeController::class . ':add')->setName('employee-add');
  $this->get('/edit', \EmployeeController::class . ':edit')->setName('employee-edit');
});

$app->group('/api/employee', function () {
  $this->get('/lists', \EmployeeApi::class . ':lists')->setName('api-employee-lists');
  $this->post('/add', \EmployeeApi::class . ':doAdd')->setName('post-api-employee-add');
  $this->post('/edit', \EmployeeApi::class . ':doEdit')->setName('post-api-employee-edit');
  $this->get('/edit', \EmployeeApi::class . ':edit')->setName('api-employee-edit');
  $this->post('/delete', \EmployeeApi::class . ':doDelete')->setName('post-api-employee-delete');
  $this->post('/reset-password', \EmployeeApi::class . ':doResetPassword')->setName('post-api-employee-reset-password');
});

$app->group('/holiday', function () {
  $this->get('', \HolidayController::class . ':lists')->setName('holiday-list');
  $this->get('/add', \HolidayController::class . ':add')->setName('holiday-add');
  $this->get('/edit', \HolidayController::class . ':edit')->setName('holiday-edit');
});

$app->group('/api/holiday', function () {
  $this->get('/lists', \HolidayApi::class . ':lists')->setName('api-holiday-lists');
  $this->post('/add', \HolidayApi::class . ':doAdd')->setName('post-api-holiday-add');
  $this->post('/edit', \HolidayApi::class . ':doEdit')->setName('post-api-holiday-edit');
  $this->get('/edit', \HolidayApi::class . ':edit')->setName('api-holiday-edit');
  $this->post('/delete', \HolidayApi::class . ':doDelete')->setName('post-api-holiday-delete');
});

$app->group('/cuti', function () {
  $this->get('', \CutiController::class . ':lists')->setName('cuti-list');
  $this->get('/add', \CutiController::class . ':add')->setName('cuti-add');
  $this->get('/edit', \CutiController::class . ':edit')->setName('cuti-edit');
});

$app->group('/api/cuti', function () {
  $this->get('/lists', \CutiApi::class . ':lists')->setName('api-cuti-lists');
  $this->post('/add', \CutiApi::class . ':doAdd')->setName('post-api-cuti-add');
  $this->post('/edit', \CutiApi::class . ':doEdit')->setName('post-api-cuti-edit');
  $this->get('/edit', \CutiApi::class . ':edit')->setName('api-cuti-edit');
  $this->post('/delete', \CutiApi::class . ':doDelete')->setName('post-api-cuti-delete');
});

$app->group('/izin', function () {
  $this->get('', \IzinController::class . ':lists')->setName('izin-list');
  $this->get('/add', \IzinController::class . ':add')->setName('izin-add');
  $this->get('/edit', \IzinController::class . ':edit')->setName('izin-edit');
});

$app->group('/api/izin', function () {
  $this->get('/lists', \IzinApi::class . ':lists')->setName('api-izin-lists');
  $this->post('/add', \IzinApi::class . ':doAdd')->setName('post-api-izin-add');
  $this->post('/edit', \IzinApi::class . ':doEdit')->setName('post-api-izin-edit');
  $this->get('/edit', \IzinApi::class . ':edit')->setName('api-izin-edit');
  $this->post('/delete', \IzinApi::class . ':doDelete')->setName('post-api-izin-delete');
});

$app->group('/overtime', function () {
  $this->get('', \OvertimeController::class . ':lists')->setName('overtime-list');
  $this->get('/add', \OvertimeController::class . ':add')->setName('overtime-add');
  $this->get('/edit', \OvertimeController::class . ':edit')->setName('overtime-edit');
});

$app->group('/api/overtime', function () {
  $this->get('/lists', \OvertimeApi::class . ':lists')->setName('api-overtime-lists');
  $this->post('/add', \OvertimeApi::class . ':doAdd')->setName('post-api-overtime-add');
  $this->post('/edit', \OvertimeApi::class . ':doEdit')->setName('post-api-overtime-edit');
  $this->get('/edit', \OvertimeApi::class . ':edit')->setName('api-overtime-edit');
  $this->post('/delete', \OvertimeApi::class . ':doDelete')->setName('post-api-overtime-delete');
});

$app->group('/schedule', function () {
  $this->get('', \ScheduleController::class . ':lists')->setName('schedule-list');
  $this->get('/add', \ScheduleController::class . ':add')->setName('schedule-add');
  $this->get('/edit', \ScheduleController::class . ':edit')->setName('schedule-edit');
});

$app->group('/api/schedule', function () {
  $this->get('/lists', \ScheduleApi::class . ':lists')->setName('api-schedule-lists');
  $this->post('/add', \ScheduleApi::class . ':doAdd')->setName('post-api-schedule-add');
  $this->post('/edit', \ScheduleApi::class . ':doEdit')->setName('post-api-schedule-edit');
  $this->get('/edit', \ScheduleApi::class . ':edit')->setName('api-schedule-edit');
  $this->post('/delete', \ScheduleApi::class . ':doDelete')->setName('post-api-schedule-delete');
});

$app->group('/api/session', function () {
  $this->post('/check', \SessionApi::class . ':doCheck')->setName('post-api-session-check');
});

$app->group('/jadwal-kerja', function () {
  $this->get('', \JadwalkerjaController::class . ':lists')->setName('jadwalkerja-list');
  $this->get('/detail', \JadwalkerjaController::class . ':detail')->setName('jadwalkerja-detail-list');
});

$app->group('/api/jadwalkerja', function () {
  $this->get('/lists', \JadwalkerjaApi::class . ':lists')->setName('api-jadwalkerja-lists');
  $this->get('/detail', \JadwalkerjaApi::class . ':detail')->setName('api-jadwalkerja-detail');
  $this->post('/edit', \JadwalkerjaApi::class . ':doEdit')->setName('post-api-jadwalkerja-edit');
  $this->get('/do-process', \JadwalkerjaApi::class . ':doProcess')->setName('api-jadwalkerja-doprocess');
});

$app->group('/setting', function () {
  $this->get('', \SettingController::class . ':lists')->setName('setting-list');
  $this->get('/add', \SettingController::class . ':add')->setName('setting-add');
  $this->get('/edit', \SettingController::class . ':edit')->setName('setting-edit');
});

$app->group('/api/setting', function () {
  $this->get('/lists', \SettingApi::class . ':lists')->setName('api-setting-lists');
  $this->post('/add', \SettingApi::class . ':doAdd')->setName('post-api-setting-add');
  $this->post('/edit', \SettingApi::class . ':doEdit')->setName('post-api-setting-edit');
  $this->get('/edit', \SettingApi::class . ':edit')->setName('api-setting-edit');
  $this->post('/delete', \SettingApi::class . ':doDelete')->setName('post-api-setting-delete');
});

$app->group('/mapping-jadwal', function () {
  $this->get('', \MappingjadwalController::class . ':lists')->setName('mappingjadwal-list');
});

$app->group('/api/mappingjadwal', function () {
  $this->get('/lists', \MappingjadwalApi::class . ':lists')->setName('api-mappingjadwal-lists');
  $this->post('/edit', \MappingjadwalApi::class . ':doEdit')->setName('post-api-mappingjadwal-edit');
});

$app->group('/report', function () {
  $this->get('/absence', \ReportabsenceController::class . ':lists')->setName('reportabsence-list');
  $this->get('/tahunan', \ReportabsenceController::class . ':listsYearly')->setName('reportabsence-listyearly');
  $this->get('/form', \ReportabsenceController::class . ':form')->setName('reportabsence-form');
  $this->get('/form-individual', \ReportabsenceController::class . ':formIndividual')->setName('reportabsence-formindividual');
});

$app->group('/role-access', function () {
  $this->get('', \RoleaccessController::class . ':lists')->setName('roleaccess-list');
  $this->get('/add', \RoleaccessController::class . ':add')->setName('roleaccess-add');
  $this->get('/edit', \RoleaccessController::class . ':edit')->setName('roleaccess-edit');
});

$app->group('/api/roleaccess', function () {
  $this->get('/lists', \RoleaccessApi::class . ':lists')->setName('api-roleaccess-lists');
  $this->post('/add', \RoleaccessApi::class . ':doAdd')->setName('post-api-roleaccess-add');
  $this->post('/edit', \RoleaccessApi::class . ':doEdit')->setName('post-api-roleaccess-edit');
  $this->get('/edit', \RoleaccessApi::class . ':edit')->setName('api-roleaccess-edit');
  $this->post('/delete', \RoleaccessApi::class . ':doDelete')->setName('post-api-roleaccess-delete');
});

$app->group('/user-admin', function () {
  $this->get('', \UseradminController::class . ':lists')->setName('useradmin-list');
  $this->get('/add', \UseradminController::class . ':add')->setName('useradmin-add');
  $this->get('/edit', \UseradminController::class . ':edit')->setName('useradmin-edit');
});

$app->group('/api/useradmin', function () {
  $this->get('/lists', \UseradminApi::class . ':lists')->setName('api-useradmin-lists');
  $this->post('/add', \UseradminApi::class . ':doAdd')->setName('post-api-useradmin-add');
  $this->post('/edit', \UseradminApi::class . ':doEdit')->setName('post-api-useradmin-edit');
  $this->get('/edit', \UseradminApi::class . ':edit')->setName('api-useradmin-edit');
  $this->post('/delete', \UseradminApi::class . ':doDelete')->setName('post-api-useradmin-delete');
});
