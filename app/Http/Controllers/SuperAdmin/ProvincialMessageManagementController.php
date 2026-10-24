<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProvincialMessage;
use App\Services\SapaanImageCompressor;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProvincialMessageManagementController extends Controller
{
    public function index(): View
    {
        return view('super-admin.sapaan.index', [
            'messages' => ProvincialMessage::query()->latest('published_at')->latest()->get(),
            'editing' => null,
            'headerTitle' => 'Sapaan & Pengumuman',
            'headerSubtitle' => 'Terbitkan pesan untuk seluruh bruder muda',
        ]);
    }

    public function edit(ProvincialMessage $message): View
    {
        return view('super-admin.sapaan.index', [
            'messages' => ProvincialMessage::query()->latest('published_at')->latest()->get(),
            'editing' => $message,
            'headerTitle' => 'Ubah Publikasi',
            'headerSubtitle' => $message->kind === 'pengumuman' ? 'Pengumuman' : 'Sapaan provinsial',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $message = ProvincialMessage::query()->create($this->attributes($request, $validated));
        $message->retireOtherSapaan();

        return redirect()->route('superadmin.sapaan.index')
            ->with('success', $message->kind === 'pengumuman'
                ? 'Pengumuman masuk ke pengingat bruder muda.'
                : 'Sapaan dipublikasikan ke beranda bruder muda.');
    }

    public function update(Request $request, ProvincialMessage $message): RedirectResponse
    {
        $validated = $this->validated($request);
        $attributes = $this->attributes($request, $validated, $message);

        if ($request->hasFile('image')) {
            if ($this->isSapaanImage($message->image)) {
                Storage::disk('public')->delete($message->image);
            }
        } elseif ($request->boolean('remove_image')) {
            if ($this->isSapaanImage($message->image)) {
                Storage::disk('public')->delete($message->image);
            }
            $attributes['image'] = null;
        }

        $message->update($attributes);
        $message->retireOtherSapaan();

        return redirect()->route('superadmin.sapaan.index')
            ->with('success', 'Publikasi diperbarui.');
    }

    public function updateStatus(ProvincialMessage $message): RedirectResponse
    {
        $message->is_active = ! $message->is_active;
        if ($message->is_active && $message->published_at === null) {
            $message->published_at = now();
        }
        $message->save();
        $message->retireOtherSapaan();

        return redirect()->route('superadmin.sapaan.index')
            ->with('success', $message->is_active ? 'Publikasi ditayangkan kembali.' : 'Publikasi ditarik dari bruder muda.');
    }

    public function destroy(ProvincialMessage $message): RedirectResponse
    {
        $kindLabel = $message->kind === 'pengumuman' ? 'Pengumuman' : 'Sapaan';

        if ($this->isSapaanImage($message->image)) {
            Storage::disk('public')->delete($message->image);
        }

        $message->delete();

        return redirect()->route('superadmin.sapaan.index')
            ->with('success', "{$kindLabel} berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'kind' => ['required', Rule::in(['sapaan', 'pengumuman'])],
            'title' => ['required', 'string', 'max:255'],
            'author_name' => ['required', 'string', 'max:255'],
            'author_role' => ['required', Rule::in([
                'Pemimpin Provinsial FIC Indonesia',
                'TPPBM',
            ])],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'tanggal_kegiatan' => ['nullable', 'date'],
            'waktu_kegiatan' => ['nullable', 'date_format:H:i'],
        ], [
            'title.required' => 'Judul wajib diisi.',
            'content.required' => 'Isi sapaan atau pengumuman wajib diisi.',
            'content.min' => 'Isi pesan minimal 10 karakter.',
            'content.max' => 'Isi pesan maksimal 5000 karakter.',
            'image.image' => 'Foto harus berupa gambar.',
            'image.max' => 'Ukuran foto maksimal 5 MB.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function attributes(Request $request, array $validated, ?ProvincialMessage $current = null): array
    {
        $kind = $validated['kind'];
        $image = $current?->image;

        if ($request->hasFile('image')) {
            $image = (new SapaanImageCompressor)->store($request->file('image'));
        }

        return [
            'kind' => $kind,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_name' => $validated['author_name'],
            'author_role' => $validated['author_role'],
            'image' => $image,
            'starts_at' => $kind === 'pengumuman' ? $this->schedule($validated) : null,
            'is_active' => true,
            'published_at' => $current?->published_at ?? now(),
            'created_by' => $current?->created_by ?? auth()->id(),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function schedule(array $validated): ?Carbon
    {
        if (empty($validated['tanggal_kegiatan'])) {
            return null;
        }

        $time = $validated['waktu_kegiatan'] ?: '00:00';

        return Carbon::parse($validated['tanggal_kegiatan'].' '.$time);
    }

    private function isSapaanImage(?string $path): bool
    {
        return is_string($path)
            && str_starts_with($path, 'sapaan/')
            && Storage::disk('public')->exists($path);
    }
}
