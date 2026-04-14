<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    /**
     * Member uploads their own avatar.
     */
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $user = auth()->user();
        $this->store($request, $user);

        return redirect()->route('profile.edit')->with('status', 'Profile photo updated.');
    }

    /**
     * Admin uploads avatar for any user.
     */
    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $user = User::findOrFail($id);
        $this->store($request, $user);

        return redirect()->back()->with('status', 'Profile photo updated.');
    }

    /**
     * Member removes their own avatar.
     */
    public function destroy()
    {
        $user = auth()->user();
        $this->remove($user);

        return redirect()->route('profile.edit')->with('status', 'Profile photo removed.');
    }

    /**
     * Admin removes avatar for any user.
     */
    public function adminDestroy($id)
    {
        $user = User::findOrFail($id);
        $this->remove($user);

        return redirect()->back()->with('status', 'Profile photo removed.');
    }

    private function store(Request $request, User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
    }

    private function remove(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }
    }

    /**
     * Member submits a base64 cropped image from the client-side cropper.
     */
    public function crop(Request $request)
    {
        $request->validate(['avatar_data' => ['required', 'string']]);

        $data = $request->input('avatar_data');

        if (!preg_match('/^data:image\/(\w+);base64,/', $data)) {
            return redirect()->route('profile.edit')->with('status', 'Invalid image data.');
        }

        $base64 = substr($data, strpos($data, ',') + 1);
        $binary = base64_decode($base64);

        if (!$binary || strlen($binary) > 5 * 1024 * 1024) {
            return redirect()->route('profile.edit')->with('status', 'Image too large or corrupt.');
        }

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $filename = 'avatars/' . \Illuminate\Support\Str::uuid() . '.jpg';
        Storage::disk('public')->put($filename, $binary);

        $user->avatar = $filename;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Profile photo updated.');
    }
}
