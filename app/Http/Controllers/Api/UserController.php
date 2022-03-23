<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Requests\User\Store;
use App\Http\Requests\User\Update;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Transformers\UserTransformer;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       try {
            $items = new User;
            
            if ($request->has('search')) {
                $searchFields = explode(',', $request->get('s_fields'));
                foreach ($searchFields as $key => $field) {
                    if (!$key) {
                        $items = $items->where($field, 'like', '%'. $request->get('search') .'%');
                    } else {
                        $items = $items->orwhere($field, 'like', '%'. $request->get('search') .'%');
                    }
                }
            }

            if ($request->page) {
                $paginator = $items->paginate($request->get('limit', 15));
                $items = $paginator->getCollection();
            } else {
                $items = $items->get();
            }

            $fractal = fractal()
            ->collection($items)
            ->transformWith(new UserTransformer);
    
            return $request->page
                ? $fractal->paginateWith(new IlluminatePaginatorAdapter($paginator))->toArray()
                : $fractal->toArray();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {
            $user = User::query()
            ->create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            ($request->has('role_name')) ? $user->assignRole($request->role_name) : $user->assignRole('forecourt_attendant');

            return response()->json($user,200);
        } catch (\Exception $exception) {
            throw $exception;
        }
        // test if user has a permission
        // $user->hasPermissionTo('product management');
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, $id)
    {
      try {
        
        $item = User::findOrFail($id);
        $item->username = $request->username;
        $item->email = $request->email;
        $item->save();

        $item->syncRoles(array(
            'name' => $request->role_name
        ));

        return response()->json($item,200); 
        } catch (\Exception $exception) {
        throw $exception;
        }
    }

    public function updatePassword(Request $request, $id)
   {
       try {
           $item = User::findOrFail($id);

               if (Hash::check($request->old_password, $item->password)) {
                   $item->password = bcrypt($request->new_password);
                   $item->save();

                    return response()->json(['status' => 'update_success']);
               } else {
                   return response()->json(['status' => 'update_failed'], 500);
               }

           } catch (\Exception $exception) {
           throw $exception;
           }
   }
    
    public function getDetails(Request $request)
    {
        $user = fractal()
            ->item($request->user())
            ->transformWith(new UserTransformer)
            ->toArray();

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $item = User::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $password
     * @return \Illuminate\Http\Response
     */
    public function adminAuthorize(Request $request)
    {
        try {
            $settings = Setting::where('rfid', $request->password)->first();
            if ($settings) {
                return response()->json($settings,200);
            } else {
                return response()->json('Wrong Password!', 401);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }


}
