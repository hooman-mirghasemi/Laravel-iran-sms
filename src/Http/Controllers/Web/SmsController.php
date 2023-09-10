<?php

namespace HoomanMirghasemi\Sms\Http\Controllers\Web;

use HoomanMirghasemi\Sms\Models\SmsReport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;

class SmsController extends Controller
{
    /**
     * Show sms in paginated list.
     * This page only use for develop.
     *
     * @return Factory|View|Application
     */
    public function index(): Factory|View|Application
    {
        /**
         * Action information.
         *
         * @get(/sms/get-sms-list)
         *
         * @name(sms.getList) the name
         *
         * @middlewares(web)
         */
        if (config('sms.dont_show_sms_list_page_condition')()) {
            abort(404);
        }
        Paginator::useBootstrap();
        $getSms = SmsReport::latest()->paginate(30);

        return view('sms::sms-list')->with('data', $getSms);
    }
}
