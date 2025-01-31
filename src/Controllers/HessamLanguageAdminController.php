<?php


namespace HessamCMS\Controllers;

use App\Http\Controllers\Controller;
use HessamCMS\Models\HessamConfiguration;
use Illuminate\Http\Request;
use HessamCMS\Helpers;
use HessamCMS\Middleware\LoadLanguage;
use HessamCMS\Middleware\UserCanManageBlogPosts;
use HessamCMS\Models\HessamLanguage;

class HessamLanguageAdminController extends Controller
{
    /**
     * HessamLanguageAdminController constructor.
     */
    public function __construct()
    {
        $this->middleware(UserCanManageBlogPosts::class);
        $this->middleware(LoadLanguage::class);

    }

    public function index(){
        $language_list = HessamLanguage::all();
        return view("hessamcms_admin::languages.index",[
            'language_list' => $language_list
        ]);
    }

    public function create_language(){
        return view("hessamcms_admin::languages.add_language");
    }

    public function store_language(Request $request){
        if ($request['locale'] == null){
            Helpers::flash_message("Select a language!");
            return view("hessamcms_admin::languages.add_language");
        }
        $language = new HessamLanguage();
        $language->active = $request['active'];
        $language->iso_code = $request['iso_code'];
        $language->locale = $request['locale'];
        $language->name = $request['name'];
        $language->date_format = $request['date_format'];
        $language->rtl = $request['rtl'];

        $language->save();

        Helpers::flash_message("Language: " . $language->name . " has been added.");
        return redirect( route('hessamcms.admin.languages.index') );
    }

    public function destroy_language(Request $request, $languageId){
        $lang = HessamLanguage::where('locale', HessamConfiguration::get('DEFAULT_LANGUAGE_LOCALE'))->first();
        if ($languageId == $lang->id){
            Helpers::flash_message("The default language can not be deleted!");
            return redirect( route('hessamcms.admin.languages.index') );
        }

        try {
            $language = HessamLanguage::findOrFail($languageId);
            //todo
//        event(new CategoryWillBeDeleted($category));
            $language->delete();
            Helpers::flash_message("The language is successfully deleted!");
            return redirect( route('hessamcms.admin.languages.index') );
        } catch (\Illuminate\Database\QueryException $e) {
            Helpers::flash_message("You can not delete this language, because it's used in posts or categoies.");
            return redirect( route('hessamcms.admin.languages.index') );
        }
    }

    public function toggle_language(Request $request, $languageId){
        $language = HessamLanguage::findOrFail($languageId);
        if ($language->active == 1){
            $language->active = 0;
        }else if ($language->active == 0){
            $language->active = 1;
        }

        $language->save();
        //todo
        //event

        Helpers::flash_message("Language: " . $language->name . " has been disabled.");
        return redirect( route('hessamcms.admin.languages.index') );
    }
    public function select_language(Request $request, $languageId){
        $language = HessamLanguage::findOrFail($languageId);
        $request->session()->put('selected_lang', $languageId);
        $request->session()->put('selected_locale', $language->locale);
        Helpers::flash_message("Language: " . $language->name . " has been selected.");
        return redirect( route('hessamcms.admin.languages.index') );
    }
}
