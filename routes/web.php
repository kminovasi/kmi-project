<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Mail;
// use App\Mail\MailNotify;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MakalahInovasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaperController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\BenefitController;
//use App\Http\Controllers\EmailController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ManagamentSystemController;
use App\Http\Controllers\BeritaAcaraController;
use App\Http\Controllers\ChartDashboardController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\FlyerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimelineContoller;
use App\Models\Flyer;
use App\Models\Timeline;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// route for login logout
Route::get('/login', [SessionController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [SessionController::class, 'login'])->name('postLogin');
Route::post('/logout', [SessionController::class, 'logout'])->name('logout')->middleware('auth');

// route for dashboard
Route::get('dashboard', [
    DashboardController::class,
    'showDashboard'
])->name('dashboard')->middleware(['auth']);

Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

Route::middleware('auth')->group(function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
    });

    Route::prefix('paper')->name('paper.')->group(function () {
        Route::get('/', [PaperController::class, 'index'])->name('index');
        Route::get('/register-team', [PaperController::class, 'registerTeam'])->name('register.team');
        Route::post('/store', [PaperController::class, 'store'])->name('register.store');
        Route::put('/update/{id}', [PaperController::class, 'update'])->name('update');
        Route::post('/store-event-external', [PaperController::class, 'storeEventExternal'])->name('register.external');

        //Route step Paper
        Route::get('/create-stages/{id}/{stage}', [PaperController::class, 'createStages'])->name('create.stages');
        Route::post('/store-stages/{id}/{stage}', [PaperController::class, 'storeStages'])->name('store.stages');
        Route::get('/show-stages/{id}/{stage}', [PaperController::class, 'showStep'])->name('show.stages');
        Route::post('/store-file-stages/{id}/{stage}', [PaperController::class, 'storeFileStages'])->name('store.file.stages');

        // Route::post('image/{stage}', [PaperController::class, 'imageStages'])->name('image.stages');

        //benefit
        Route::post('/store-benefit/{id}/', [PaperController::class, 'storeBenefit'])->name('store.benefit');

        //approval
        Route::get('approve-paper/{id}', [PaperController::class, 'approvePaper'])->name('approve.paper');
        Route::get('reject-paper/{id}', [PaperController::class, 'rejectPaper'])->name('reject.paper');
        Route::get('/approve-fasil-paper/{id}', [PaperController::class, 'approvePaperFasil'])->name('approvePaperFasil');
        Route::put('/approve-fasil-paper/{id}', [PaperController::class, 'approvePaperFasil'])->name('approvePaperFasil');

        Route::get('approve-benefit/{id}', [PaperController::class, 'approveBenefit'])->name('approve.benefit');
        Route::get('reject-benefit/{id}', [PaperController::class, 'rejectBenefit'])->name('reject.benefit');
        Route::get('/approve-fasil-benefit/{id}', [PaperController::class, 'approveBenefitFasil'])->name('approveBenefitFasil');
        Route::put('/approve-fasil-benefit/{id}', [PaperController::class, 'approveBenefitFasil'])->name('approveBenefitFasil');

        Route::get('approve-benefitGM/{id}', [PaperController::class, 'approvebenefitbyGM'])->name('approve.benefitGM');
        Route::get('reject-benefitGM/{id}', [PaperController::class, 'rejectbenefitbyGM'])->name('reject.benefitGM');
        Route::get('/approve-generalmanager-benefit/{id}', [PaperController::class, 'approveBenefitGM'])->name('approveBenefitGM');
        Route::put('/approve-generalmanager-benefit/{id}', [PaperController::class, 'approveBenefitGM'])->name('approveBenefitGM');

        Route::put('/approve-admin-paper/{id}', [PaperController::class, 'approvePaperAdmin'])->name('approveadmin');

        //email approval
        //Route::post('/send-email/{id}', [PaperController::class, 'approvePaperFasil'])->name('send.email.approval');

        //competition
        Route::get('/event', [PaperController::class, 'event'])->name('event');
        Route::post('/assign-new-event', [PaperController::class, 'assign_new_event'])->name('assign.new.event');
        Route::post('/rollback/{id}', [PaperController::class, 'rollback_status'])->name('rollback');
        //Route::post('/rollback-paper/{id}', [PaperController::class, 'rollbackPaper'])->name('rollback.paper'); //baru
        //Route::post('/rollback-benefit/{id}', [PaperController::class, 'rollbackBenefit'])->name('rollback.benefit');

        Route::post('/upload_document', [PaperController::class, 'uploadDocument'])->name('uploadDocument');
        Route::delete('/delete_document', [PaperController::class, 'deleteDocument'])->name('deleteDocument');

        Route::get('/detail_paper/{team_id}', [PaperController::class, 'detail_paper'])->name('detailpaper');
    });

    route::prefix('query')->name('query.')->group(function () {
        Route::post('/search', [QueryController::class, 'search'])->name('search');
        Route::post('/autocomplete', [QueryController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/custom-get', [QueryController::class, 'custom_get'])->name('custom');
        Route::get('/custom-getassesment', [QueryController::class, 'custom_getAssesment'])->name('customassesment');
        Route::get('/get_role', [QueryController::class, 'get_role'])->name('get_role');
        Route::get('/getFile', [QueryController::class, 'getFile'])->name('getFile');
        Route::post('/get_data_member', [QueryController::class, 'get_data_member'])->name('get_data_member');

        // benefit admin
        // Route::post('/get_benefit', [QueryController::class, 'get_benefit'])->name('get_benefit');
        Route::post('/add_benefit', [QueryController::class, 'add_benefit'])->name('add_benefit');
        Route::put('/update_benefit', [QueryController::class, 'update_benefit'])->name('update_benefit');
        Route::delete('/delete_benefit', [QueryController::class, 'delete_benefit'])->name('delete_benefit');

        //get api view data ajax with yajra datatable
        Route::get('/getmakalah', [QueryController::class, 'get_data_makalah'])->name('getmakalah');
        Route::get('/get-template-assessment', [QueryController::class, 'get_data_template_assessment'])->name('get.template.assessment');
        Route::get('/get-point-assessment', [QueryController::class, 'get_data_point_assessment'])->name('get.point.assessment');
        Route::post('/get_point', [QueryController::class, 'get_point'])->name('get_point_assessment');
        Route::get('/get_assessment_juri', [QueryController::class, 'get_data_assessment_juri'])->name('get_assessment_juri');
        Route::get('/get-berita-acara', [QueryController::class, 'get_berita_acara'])->name('get.berita_acara');

        Route::get('/get_data_history/{team_id}', [QueryController::class, 'get_data_history'])->name('get.data.history');
        Route::get('/coba', [QueryController::class, 'coba'])->name('coba');

        Route::get('/get_competition', [QueryController::class, 'get_competition'])->name('get_competition');
        Route::get('/get_evidence', [QueryController::class, 'get_evidence'])->name('get_evidence');
        Route::get('/get_oda_assessment', [QueryController::class, 'get_oda_assessment'])->name('get_oda_assessment');
        Route::get('/get_pa_assessment', [QueryController::class, 'get_pa_assessment'])->name('get_pa_assessment');
        Route::get('/get_input_oda_assessment_team', [QueryController::class, 'get_input_oda_assessment_team'])->name('get_input_oda_assessment_team');
        Route::get('/get_input_pa_assessment_team', [QueryController::class, 'get_input_pa_assessment_team'])->name('get_input_pa_assessment_team');
        Route::get('/get_input_caucus_assessment_team', [QueryController::class, 'get_input_caucus_assessment_team'])->name('get_input_caucus_assessment_team');
        Route::get('/get_fix_assessment', [QueryController::class, 'get_fix_assessment'])->name('get_fix_assessment');
        Route::get('/get_judge', [QueryController::class, 'get_judge'])->name('get_judge');
        Route::get('/get_event', [QueryController::class, 'get_event'])->name('get_event');
        Route::get('/get_bod_event', [QueryController::class, 'get_bod_event'])->name('get_bod_event');
        Route::get('/get_caucus', [QueryController::class, 'get_caucus'])->name('get_caucus');
        Route::get('/get_presentasi_bod', [QueryController::class, 'get_presentasi_bod'])->name('get_presentasi_bod');
        Route::get('/get_penetapan_juara', [QueryController::class, 'get_penetapan_juara'])->name('get_penetapan_juara');
        Route::get('/get_fasilitator', [QueryController::class, 'get_fasilitator'])->name('get_fasilitator');
        Route::post('/get_GM', [QueryController::class, 'get_GM'])->name('get_GM');
        Route::post('/get_BOD', [QueryController::class, 'get_BOD'])->name('get_BOD');
    });

    Route::prefix('assessment')->name('assessment.')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('index.juri');
        Route::get('/value', [AssessmentController::class, 'index'])->name('index.admin');
        Route::get('/show-template', [AssessmentController::class, 'showTemplate'])->name('show.template');
        Route::get('/create-template', [AssessmentController::class, 'createTemplate'])->name('create.template');
        Route::post('/store-template', [AssessmentController::class, 'storeTemplate'])->name('store.template');
        Route::put('/update-template/{id}', [AssessmentController::class, 'updateTemplate'])->name('update.template');
        Route::delete('/delete-template/{id}', [AssessmentController::class, 'deleteTemplate'])->name('delete.template');

        Route::get('/show-assessment-point', [AssessmentController::class, 'showAssessmentPoint'])->name('show.point');
        Route::post('/assign-point', [AssessmentController::class, 'storeAssignPoint'])->name('store.assign.point');
        Route::put('/update-point/{id}', [AssessmentController::class, 'updateAssessmentPoint'])->name('update.point');
        Route::delete('/delete-point/{id}', [AssessmentController::class, 'deleteAssessmentPoint'])->name('delete.point');
        Route::put('/update-status', [AssessmentController::class, 'changeStatusAssessmentPoint'])->name('update.status');

        Route::get('/assessment-ondesk-value/{id}', [AssessmentController::class, 'assessmentValue_oda'])->name('juri.value.oda');
        Route::get('/assessment-presentation-value/{id}', [AssessmentController::class, 'assessmentValue_pa'])->name('juri.value.pa');
        Route::get('/assessment-caucus-value/{id}', [AssessmentController::class, 'assesmentValue_caucus'])->name('juri.value.caucus');
        Route::put('/input-nilai-score', [AssessmentController::class, 'scoreJuri'])->name('input.score');
        Route::post('/addJuri', [AssessmentController::class, 'addJuri'])->name('addJuri');
        Route::post('/deleteJuri', [AssessmentController::class, 'deleteJuri'])->name('deleteJuri');
        Route::put('/submitJuri/{id}', [AssessmentController::class, 'submitJuri'])->name('submitJuri');

        Route::get('/On-Desk', [AssessmentController::class, 'on_desk'])->name('on_desk');
        Route::get('/Presentation', [AssessmentController::class, 'presentation'])->name('presentation');
        // Route::get('/fixation-assessment', [AssessmentController::class, 'fixation'])->name('fixation');
        Route::get('/show-ondesk-sofi/{id}', [AssessmentController::class, 'showSofi_oda'])->name('show.sofi.oda');
        Route::get('/show-presentation-sofi/{id}', [AssessmentController::class, 'showSofi_pa'])->name('show.sofi.pa');
        Route::get('/show-caucus-sofi/{id}', [AssessmentController::class, 'showSofi_caucus'])->name('show.sofi.caucus');

        Route::get('/download-ondesk-sofi/{id}', [AssessmentController::class, 'downloadSofi_oda'])->name('download.sofi.oda');
        Route::get('/download-presentation-sofi/{id}', [AssessmentController::class, 'downloadSofi_pa'])->name('download.sofi.pa');

        Route::put('/fix-oda', [AssessmentController::class, 'oda_fix'])->name('fix.oda');
        Route::put('/fix-pa', [AssessmentController::class, 'pa_fix'])->name('fix.pa');
        Route::put('/fix-eligible', [AssessmentController::class, 'eligible_fix'])->name('fix.eligible');
        Route::put('/to-caucus', [AssessmentController::class, 'to_caucus_fix'])->name('caucus');
        Route::get('/caucus', [AssessmentController::class, 'caucus'])->name('caucus.data');
        Route::post('/summaryExecutive', [AssessmentController::class, 'summaryExecutive'])->name('summaryExecutive');
        Route::put('/summaryExecutivePPT', [AssessmentController::class, 'summaryPPT'])->name('summaryPPT');
        Route::get('/presentasiBOD', [AssessmentController::class, 'presentasiBOD'])->name('presentasiBOD');
        Route::get('/penetapanJuara', [AssessmentController::class, 'penetapanJuara'])->name('penetapanJuara');
        Route::post('/addBODvalue', [AssessmentController::class, 'addBODvalue'])->name('addBODvalue');
        Route::post('/keputusanBOD', [AssessmentController::class, 'keputusanBOD'])->name('keputusanBOD');
        Route::get('/pdf-summary/{team_id}', [AssessmentController::class, 'pdfSummary'])->name('pdfSummary');
    });

    Route::prefix('berita-acara')->name('berita-acara.')->group(function () {
        Route::get('/', [BeritaAcaraController::class, 'index'])->name('index');
        Route::get('/create', [BeritaAcaraController::class, 'create'])->name('create');
        Route::post('store', [BeritaAcaraController::class, 'store'])->name('store');
        Route::get('/downloadPDF/{id}', [BeritaAcaraController::class, 'downloadPDF'])->name('downloadPDF');
        Route::get('/showPDF/{id}', [BeritaAcaraController::class, 'showPDF'])->name('showPDF');
    });

    Route::prefix('event')->name('event.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/externalEvent', [EventController::class, 'externalEvent'])->name('externalEvent');
        Route::post('/externalEvent-store', [EventController::class, 'externalEventStore'])->name('externalEventStore');
    });

    Route::middleware(['role:Superadmin,Admin'])->prefix('management-system')->name('management-system.')->group(function () {
        Route::get('/', [ManagamentSystemController::class, 'index'])->name('index');
        Route::get('/assign-juri', [ManagamentSystemController::class, 'assignJuri'])->name('assign.juri');
        Route::get('/assign-juri-create', [ManagamentSystemController::class, 'assignJuriCreate'])->name('assign.juri.create');
        Route::post('/assign-juri-store', [ManagamentSystemController::class, 'assignJuriStore'])->name('assign.juri.store');
        Route::post('/assign-juri', [ManagamentSystemController::class, 'assignJuri'])->name('assign.juri');
        Route::put('/assign-juri', [ManagamentSystemController::class, 'assignJuri'])->name('assign.juri');
        Route::put('/revoke-juri/{id}', [ManagamentSystemController::class, 'revokeJuri'])->name('revoke.juri');
        Route::put('/update-juri', [ManagamentSystemController::class, 'updateJuri'])->name('update.juri');
        Route::get('/update-juri', [ManagamentSystemController::class, 'updateJuri'])->name('update.juri');
        Route::get('/assign-event', [ManagamentSystemController::class, 'assignEvent'])->name('assign.event');
        Route::get('/assign-event-create', [ManagamentSystemController::class, 'assignEventCreate'])->name('assign.event.create');
        Route::post('/assign-event-store', [ManagamentSystemController::class, 'assignEventStore'])->name('assign.event.store');
        Route::put('/change-event/{id}', [ManagamentSystemController::class, 'changeEvent'])->name('change.event');
        Route::put('/update-event/{id}', [ManagamentSystemController::class, 'updateEvent'])->name('update.event');
        Route::get('/get-events', [ManagamentSystemController::class, 'getEvents'])->name('get-events');

        Route::get('/assign-role', [ManagamentSystemController::class, 'indexRole'])->name('role.index');
        Route::get('/assign-role/add', [ManagamentSystemController::class, 'roleAssignAdd'])->name('role.assign.add');
        Route::put('/assign-role/store', [ManagamentSystemController::class, 'roleAssignStore'])->name('role.assign.store');
        Route::get('/assign-role/bod-index', [ManagamentSystemController::class, 'indexBOD'])->name('role.bod.index');
        Route::get('/assign-role/bod-add', [ManagamentSystemController::class, 'createAssignBOD'])->name('role.bod.event.create');
        Route::post('/assign-role/bod-add', [ManagamentSystemController::class, 'storeAssignBOD'])->name('role.bod.event.store');

        Route::get('/assign-role/innovator', [ManagamentSystemController::class, 'indexInnovator'])->name('role.innovator.index');
        Route::get('/assign-role/admin', [ManagamentSystemController::class, 'indexAdmin'])->name('role.admin.index');
        Route::get('/assign-role/superAdmin', [ManagamentSystemController::class, 'indexSuperAdmin'])->name('role.superadmin.index');

        Route::prefix('team')->name('team.')->group(function () {
            //category
            Route::get('/category', [ManagamentSystemController::class, 'categoryIndex'])->name('category.index');
            Route::post('/category-store', [ManagamentSystemController::class, 'categoryStore'])->name('category.store');
            Route::put('/category-update/{id}', [ManagamentSystemController::class, 'categoryUpdate'])->name('category.update');
            Route::delete('/category-delete/{id}', [ManagamentSystemController::class, 'categoryDelete'])->name('category.delete');

            //tema
            Route::get('/theme', [ManagamentSystemController::class, 'themeIndex'])->name('theme.index');
            Route::post('/theme-store', [ManagamentSystemController::class, 'themeStore'])->name('theme.store');
            Route::put('/theme-update/{id}', [ManagamentSystemController::class, 'themeUpdate'])->name('theme.update');
            Route::delete('/theme-delete/{id}', [ManagamentSystemController::class, 'themeDelete'])->name('theme.delete');

            //company
            Route::get('/company', [ManagamentSystemController::class, 'companyIndex'])->name('company.index');
            Route::post('/company-store', [ManagamentSystemController::class, 'companyStore'])->name('company.store');
            Route::put('/company-update/{id}', [ManagamentSystemController::class, 'companyUpdate'])->name('company.update');
            Route::delete('/company-delete/{id}', [ManagamentSystemController::class, 'companyDelete'])->name('company.delete');
        });
    });


    // Group Route Superadmin and Admin
    Route::middleware([])->group(function () {
        // Rute Benefit
        Route::prefix('benefit')->name('benefit.')->group(function () {
            Route::get('/', [BenefitController::class, 'createBenefitAdmin'])->name('index');
            Route::get('/create/{id}', [BenefitController::class, 'createBenefitUser'])->name('create.user');
            Route::post('/store/{id}/', [BenefitController::class, 'storeBenefitUser'])->name('store.user');
        });

        // Rute Flyer
        Route::resource('flyer', FlyerController::class)->only(['index', 'store', 'destroy']);

        //Rute Timeline
        Route::resource('timeline', TimelineContoller::class)->only(['index', 'store', 'destroy']);
    });



    // Evidence
    Route::prefix('/evidence')->name('evidence.')->group(function () {
        Route::get('/', [EvidenceController::class, 'index'])->name('index');
        Route::get('/category/{id}', [EvidenceController::class, 'list_winner'])->name('category');
        Route::get('/detail-team', [EvidenceController::class, 'team_detail'])->name('detail');
    });


    Route::prefix('/dokumentasi')->name('dokumentasi.')->group(function () {
        Route::get('/', [DokumentasiController::class, 'index'])->name('index');
        //berita
        Route::prefix('/berita-acara')->name('berita-acara.')->group(function () {
            Route::get('/', [DokumentasiController::class, 'indexBeritaAcara'])->name('index');
            Route::get('/store', [DokumentasiController::class, 'storeBeritaAcara'])->name('store');
            Route::put('/upload/{id}', [DokumentasiController::class, 'uploadBeritaAcara'])->name('upload');
        });
    });
    Route::prefix('chart')->name('chart.')->group(function () {
        Route::get('/semenTeamChart', [ChartDashboardController::class, 'semenTeamChart'])->name('semenTeamChart');
        Route::get('/nonSemenTeamChart', [ChartDashboardController::class, 'NonSemenTeamChart'])->name('NonSemenTeamChart');
        Route::get('/realisasiTeamChart', [ChartDashboardController::class, 'realisasiTeamChart'])->name('realisasiTeamChart');
        Route::get('/realisasiKaryawanChart', [ChartDashboardController::class, 'realisasiKaryawanChart'])->name('realisasiKaryawanChart');
        Route::get('/benefitTeamChart', [ChartDashboardController::class, 'benefitTeamChart'])->name('benefitTeamChart');
    });
});

Route::get('/ck5', function () {
    return view('coba.coba');
});

Route::get('/', function () {
    $flyer = Flyer::all();
    $timeline = Timeline::all();
    return view('landingpage', [
        'flyer' => $flyer,
        'timeline' => $timeline
    ]);
});

Route::get('/testing', function () {
    return view('testmerge');
});

Route::post('merger-pdf', [App\Http\Controllers\PDFMergerController::class, 'merge'])->name('merge-pdf');
