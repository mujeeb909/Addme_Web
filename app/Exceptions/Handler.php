<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // if ($e instanceof QueryException) {
            //     // Display custom database error page or redirect to a specific route
            //     // return response()->view('errors.404', [], 500);
            //     echo view('errors.404');
            //     exit;
            // }

            // pre_print($e);
            // return response()->view('errors.404', [], 404);
            echo '<div class="container">
                    <div class="footer-height-offset d-flex justify-content-center align-items-center flex-column">
                        <div class="row align-items-sm-center w-100">
                            <div class="col-sm-6">
                                <div class="text-center text-sm-right mr-sm-4 mb-5 mb-sm-0">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4 text-center text-sm-left">
                                <h1 class="display-1 mb-0" style="text-align: center;">404 page not found!</h1>
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                </div>';
            // exit;
        });
    }
}
