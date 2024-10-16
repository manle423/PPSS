<?php

namespace App\Http\Controllers\Admin;
use App\Models\StoreInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
    }
    public function index()
    {
        return view('admin.dashboard');
    }
    public function changePass()
    {
        return view('admin.change-pass');
    }
    public function showInfo(){
        $storeInfo = StoreInfo::first();  
        return view('admin.shop.show-info', compact('storeInfo'));
    }
    public function edit()
    {
        $storeInfo = StoreInfo::first();  
        return view('admin.shop.info', compact('storeInfo'));
    }
    
  
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'team' => 'nullable|string',
            'email' => 'nullable|email',
            'footer_why_people_like_us' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'product_category' => 'nullable|string',
            'trusted' => 'nullable|string',
            'quality' => 'nullable|string',
            'price' => 'nullable|string',
            'delivery' => 'nullable|string',
            'thanks' => 'nullable|string',
        ]);
    
        $storeInfo = StoreInfo::firstOrFail(); 
   
        $storeInfo->update($validated);
    
        if ($request->hasFile('logo')) {
        
            $logoPath = $request->file('logo')->store('logo', 'public');
            $storeInfo->update(['logo' => $logoPath]);
            $this->setEnvValue('STORE_LOGO', "$logoPath"); 
        } else {
           
            $this->setEnvValue('STORE_LOGO', $storeInfo->logo);
        }
       
        if (!empty($validated['team'])) {
            $this->setEnvValue('TEAM_NAME', '"'.$validated['team'].'"');
        }
        return redirect()->back()->with('success', 'Update shop information successfully!');
    }
    
    
    protected function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
           
            $env = file_get_contents($path);

            if (strpos($env, "$key=") !== false) {
                $env = preg_replace("/^$key=.*$/m", "$key=$value", $env);
            } else {
              
                $env .= "\n$key=$value";
            }

            file_put_contents($path, $env);
        }


}
}