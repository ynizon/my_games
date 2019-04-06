<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
		\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {    
		if ($request->ajax()) {
			return response()->json(['exception' => $exception]);
		}

	
		if($this->isHttpException($exception)) {
			switch ($exception->getStatusCode()) {

				// not authorized
				case '403':
					return \Response::view('errors.403',['exception'=> $exception],403);
					break;

				// not found
				case '404':
					return \Response::view('errors.404',['exception'=> $exception],404);
					break;

				// internal error
				case '500':
					return \Response::view('errors.500',['exception'=> $exception],500);
					break;

				default:
					return $this->renderHttpException($exception);
					break;
			}
		}
		else
		{
			if ($exception instanceof TokenMismatchException){
				return redirect()->back()->withInput($request->except('_token'))
				->withError('Votre session a expirée. Merci de recommencer.');
			}			
			else{
				if ($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
					return \Response::view('errors.posttoolarge',['exception'=> $exception],500);
				}else{
					return parent::render($request, $exception);	
				}
			}
		}
		/*
		if ($exception->getMessage() == "Unauthenticated."){
			//Gestion derreur Par defaut (renvoie au login)
			return parent::render($request, $exception);
		}else{
			return response()->view('errors/index', ['exception'=> $exception],500);	
		}
		*/
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
