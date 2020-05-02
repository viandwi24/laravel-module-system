<?php
namespace Viandwi24\ModuleSystem\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Viandwi24\ModuleSystem\Facades\Core;
use Viandwi24\ModuleSystem\Facades\Module;
use ZipArchive;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::get();
        return view('ModuleSystem::index', compact('modules'));
    }

    public function enable($module)
    {
        $enable = Module::enable($module);
        return redirect()->back();
    }

    public function disable($module)
    {
        $disable = Module::disable($module);
        return redirect()->back();
    }

    public function install(Request $request)
    {
        $request->validate([ 'zip' => 'required|file|mimes:zip' ]);

        $zip = new ZipArchive;
        $res = $zip->open($request->zip);
        if ($res === TRUE) {
            $zip->extractTo(Module::getPath());
            $zip->close();
            Core::indexModule(true);
            return redirect()->back();
        } else {
            return redirect()->back()->withErrors('file', 'failed');
        }
    }
}