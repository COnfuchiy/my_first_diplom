<?php


namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteSaveRequest;
use App\Http\Requests\SiteUpdateRequest;
use App\Jobs\PerformanceMonitoringJob;
use App\Models\DataTableModel;
use App\Models\MonitoringSite;
use App\Models\Page;
use App\Models\TelegramChats;
use App\Monitoring\PerformanceMonitoringJobsDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;


class SiteListController extends Controller
{
    use DataTableModel;

    /*
     * Display a listing of monitoring sites
     */
    public function index(Request $request)
    {
        // Get all user sites
        $userSitesQuery = Auth::user()->sites->toQuery();

        // Render site list
        return $this->processingDataTable(
            'sites',
            $request,
            $userSitesQuery,
            MonitoringSite::class,
            'date',
            'desc'
        );
    }

    /*
     * Show the form for creating a new monitoring site
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $chats = TelegramChats::where('user_id', Auth::id())->where('chat_confirm',1)->get();
        return view('sites-list.create', ['site' => new MonitoringSite(), 'chats' => $chats]);
    }

    /*
     * Create a new monitoring site.
     */
    public function store(SiteSaveRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();
        $site = new MonitoringSite(array_merge($validatedData, ['user_id' => Auth::id()]));
        if (!$site->save()) {
            //
        }
        if ($validatedData['page_select_type'] === 'manual') {
            $urls = explode("\n", str_replace("\r", '', $validatedData['manual_input_text_area']));
            foreach ($urls as $url) {
                $url = trim($url);
                $sitePage = new Page(['site_id' => $site->id, 'path' => parse_url($url, PHP_URL_PATH)]);
                if (!$sitePage->save()) {
                    //
                }
            }
        }
//        if($site->seo_psi_desktop_check || $site->seo_psi_mobile_check){
//            PerformanceMonitoringJob::dispatch($site);
//        }
//        MonitoringJobsDistributor::addToQueue($site);
        return redirect()->action([$this::class, 'index']);
    }

    public function destroy(MonitoringSite $site)
    {
        return $site->delete();
    }

    public function activity(MonitoringSite $site, Request $request)
    {
        $request->validate(['isActive' => 'boolean']);
        $site->is_active = $request->isActive;
        if ($request->isActive) {
            PerformanceMonitoringJobsDistributor::addToQueue($site);
        } else {
            PerformanceMonitoringJobsDistributor::removeFromQueue($site);
        }
        return $site->save();
    }

    public function edit(MonitoringSite $site)
    {
        $chats = TelegramChats::where('user_id', Auth::id())->get();
        return view('sites-list.edit', compact('chats', 'site'));
    }

    public function update(MonitoringSite $site, SiteSaveRequest $request)
    {
        $validatedData = $request->validated();
        // add new monitoring period logic
        $site->update($validatedData);
        if ($validatedData['page_select_type'] === 'manual') {
            $site->sitemap_url = null;
            $updatedUrls = explode("\n", str_replace("\r", '', $validatedData['manual_input_text_area']));
            foreach ($updatedUrls as &$url) {
                $url = parse_url(trim($url), PHP_URL_PATH);
            }
            $site->updatePages($updatedUrls);
        }
        else{
            $site->deleteAllPages();
        }
        if (!$site->save()) {
            //
        }
        if($site->seo_psi_desktop_check || $site->seo_psi_mobile_check){
            PerformanceMonitoringJob::dispatch($site);
        }
        return redirect()->action([$this::class, 'index']);
    }

    public function search(Request $request)
    {
        $request->validate(['searchRequest'=>'string']);
        $searchRequest = $request->searchRequest;
        if(parse_url($searchRequest)!==false){
            $searchRequest = parse_url($searchRequest,PHP_URL_HOST);
        }
        $searchResults = MonitoringSite::where('domain', 'like', "%$searchRequest%")->simplePaginate($this->pageSize);
        $totalSitesCount = $searchResults->count();
        $totalSitesPageCount = (int)ceil($totalSitesCount / $this->pageSize);
        $currentPage = $request->page ?? 1;
        return view(
            'components.sites-table',
            compact(
                'searchResults',
                'totalSitesCount',
                'totalSitesPageCount',
                'currentPage'
            )
        );
//        return view('sites-list.create', ['site' => new MonitoringSite(), 'chats' => $chats]);
    }
}
