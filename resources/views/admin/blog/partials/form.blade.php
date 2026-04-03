<div class="card" style="max-width:900px;">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul style="margin:0; padding-left:1.15rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="mb-3">
                <label class="form-label-admin">Tiêu đề <span style="color:var(--danger)">*</span></label>
                <input type="text" name="title" class="form-control" required value="{{ old('title', $post->title ?? '') }}" placeholder="VD: 7 mẹo tối ưu điểm thưởng AquaHub">
            </div>

            <div class="grid-2 mb-3">
                <div>
                    <label class="form-label-admin">Slug (URL thân thiện)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug ?? '') }}" placeholder="de-trong-he-thong-tu-sinh-neu-de-trong">
                </div>
                <div>
                    <label class="form-label-admin">Ảnh cover (tùy chọn)</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                </div>
            </div>

            @if(!empty($post?->cover_image_url))
                <div class="mb-3" style="display:flex; gap:0.8rem; align-items:center;">
                    <img src="{{ $post->cover_image_url }}" alt="cover" style="width:120px; height:72px; object-fit:cover; border-radius:8px; border:1px solid var(--border);">
                    <label style="display:flex; align-items:center; gap:0.4rem; font-size:0.86rem; color:var(--text-muted);">
                        <input type="checkbox" name="remove_cover_image" value="1"> Xóa ảnh cover hiện tại
                    </label>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label-admin">Mô tả ngắn (excerpt)</label>
                <textarea name="excerpt" rows="3" class="form-control" placeholder="Đoạn mô tả xuất hiện ở danh sách bài viết và có thể dùng cho SEO.">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Nội dung bài viết <span style="color:var(--danger)">*</span></label>
                <textarea id="blog-content-editor" name="content" rows="15" class="form-control" required placeholder="Viết nội dung chi tiết...">{{ old('content', $post->content ?? '') }}</textarea>
                <small style="display:block; margin-top:0.45rem; color:var(--text-muted); font-size:0.78rem;">
                    Có thể dùng tiêu đề, đậm/nghiêng, danh sách, link, blockquote, bảng và chèn ảnh bằng URL.
                </small>
            </div>

            <div class="grid-2 mb-3">
                <div>
                    <label class="form-label-admin">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title ?? '') }}" placeholder="Tiêu đề SEO (tối đa 60-70 ký tự)">
                </div>
                <div>
                    <label class="form-label-admin">Meta Description</label>
                    <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $post->meta_description ?? '') }}" placeholder="Mô tả SEO (khoảng 140-160 ký tự)">
                </div>
            </div>

            <div class="mb-3">
                <label style="display:inline-flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published ?? false) ? 'checked' : '' }}>
                    <span>Publish ngay sau khi lưu</span>
                </label>
            </div>

            <div style="display:flex; gap:0.75rem; margin-top:1.5rem;">
                <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2"></i> Lưu bài viết
                </button>
            </div>
        </form>
    </div>
</div>

@push('admin-styles')
<style>
    .form-label-admin {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
        font-weight: 500;
    }

    .ck.ck-editor {
        color: #111827;
    }

    .ck.ck-editor__main > .ck-editor__editable {
        min-height: 340px;
        background: #f9fafb;
        border-radius: 0 0 8px 8px;
    }

    .ck.ck-toolbar {
        border-radius: 8px 8px 0 0;
    }
</style>
@endpush

@push('admin-scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editorElement = document.getElementById('blog-content-editor');

        if (!editorElement || typeof ClassicEditor === 'undefined') {
            return;
        }

        ClassicEditor.create(editorElement, {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'link', 'blockQuote', 'insertTable', 'imageInsert', '|',
                'undo', 'redo'
            ],
            link: {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://'
            },
            image: {
                toolbar: ['imageTextAlternative', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side']
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        }).catch(function (error) {
            console.error('CKEditor init failed:', error);
        });
    });
</script>
@endpush
