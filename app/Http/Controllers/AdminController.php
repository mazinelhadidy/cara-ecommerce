<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.dashboard', [
            'products' => Product::withTrashed()->latest()->paginate(12),
            'orders' => Order::with('user')->latest()->take(10)->get(),
            'messages' => ContactMessage::latest()->take(10)->get(),
        ]);
    }

    public function categories(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.categories', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate(['name' => ['required', 'string', 'max:255']]);

        Category::create(['name' => $request->input('name')]);

        return back()->with('status', 'Category created successfully.');
    }

    public function products(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.products', [
            'products' => Product::withTrashed()->with('category')->latest()->paginate(15),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeAdminProductImage($request->file('image'));
        }

        Product::create($data);

        return back()->with('status', 'Product created successfully.');
    }

    public function editProduct(Product $product): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.edit-product', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeAdminProductImage($request->file('image'));
        }

        $product->update($data);

        return redirect()->route('admin.products')->with('status', 'Product updated successfully.');
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $product->delete();

        return back()->with('status', 'Product moved to trash.');
    }

    public function restoreProduct(int $product): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $product = Product::withTrashed()->findOrFail($product);
        $product->restore();

        return back()->with('status', 'Product restored successfully.');
    }

    public function forceDeleteProduct(int $product): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $product = Product::withTrashed()->findOrFail($product);
        $product->forceDelete();

        return back()->with('status', 'Product permanently deleted.');
    }

    private function storeAdminProductImage(object $image): string
    {
        $directory = public_path('admin/upload');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $image->getClientOriginalExtension();
        $image->move($directory, $filename);

        return "admin/upload/{$filename}";
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        ContactMessage::create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

        return back()->with('status', 'Your message has been sent to the admin.');
    }

    public function contactMessages(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.contact-messages', [
            'messages' => ContactMessage::with('user')->latest()->paginate(15),
        ]);
    }

    public function markMessageRead(ContactMessage $message): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $message->update(['is_read' => true]);

        return back();
    }

    public function orderIndex(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.orders', [
            'orders' => Order::with('user')->latest()->paginate(15),
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,shipped,cancelled'],
        ]);

        $order->update($validated);
        $order->user->notify(new OrderStatusUpdated($order));

        return back()->with('status', 'Order status updated and user notified.');
    }
}
