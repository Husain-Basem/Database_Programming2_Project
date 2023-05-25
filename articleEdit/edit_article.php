<?php
declare(strict_types=1);
include_once '../prelude.php';

if (isset($_GET['articleId'])) {
  $article = Article::from_articleId((int) $_GET['articleId']);
}

$pageTitle = 'Article Edit';
$headerIncludes = '
    <link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />
    <link rel="stylesheet" href="' . BASE_URL . '/css/quill.imageUploader.min.css" />
    <script src="' . BASE_URL . '/js/quill.min.js"></script>
    <script src="' . BASE_URL . '/js/quill.imageUploader.min.js"></script>
    <script src="' . BASE_URL . '/js/quill-blot-formatter.min.js"></script>
';
include PROJECT_ROOT . '/header.html';

?>


<div class="container">
  <div class="row align-items-center mb-3">
    <div class="col-6 hstack gap-2">
      <input type="text" name="title" id="title" class="form-control" required value="<?= $article->title ?>">
      <label for="category" class="form-label">Category:</label>
      <select class="form-select w-50" name="category" id="category">
        <option <?= $article->category == 'local' ? 'selected' : '' ?> value="local">Local News</option>
        <option <?= $article->category == 'international' ? 'selected' : '' ?> value="international">International News
        </option>
        <option <?= $article->category == 'economy' ? 'selected' : '' ?> value="economy">Economy News</option>
        <option <?= $article->category == 'tourism' ? 'selected' : '' ?> value="tourism">Tourism</option>
      </select>
    </div>
    <div class="col hstack gap-3">
      <button id="saveBtn" class="btn btn-primary">Save</button>
      <button id="deleteBtn" class="btn btn-danger ms-auto" data-bs-toggle="modal"
        data-bs-target="#deleteModal">Delete</button>
      <button id="publishBtn" class="btn btn-primary" data-bs-toggle="modal"
        data-bs-target="#publishModal">Publish</button>
    </div>
  </div>

  <div class="d-flex flex-column" style="max-height: calc(100vh - 9rem)">
    <div id="editor" class="h-100 overflow-auto">
    </div>
    <pre id="template" class="visually-hidden"><?= $article->content ?></pre>
  </div>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitleId">Delete confirmation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this article? This action is not reversible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

          <form action="<?= BASE_URL . '/articleEdit/delete_article.php' ?>" method="post">
            <button type="submit" class="btn btn-danger">Delete</button>
            <input type="hidden" name="articleId" value="<?= $article->articleId ?>">
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Publish Modal -->
  <div class="modal fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="pmodalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pmodalTitleId">Publish Article</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to publish this article? Once an article is published, it can no longer be edited.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

          <form action="<?= BASE_URL . '/articleEdit/publish_article.php' ?>" method="post">
            <button id="publishSubmit" type="submit" class="btn btn-primary">Publish</button>
            <input type="hidden" name="articleId" value="<?= $article->articleId ?>">
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  $(() => {
    Quill.register('modules/imageUploader', ImageUploader);
    Quill.register('modules/blotFormatter', QuillBlotFormatter.default);

    var toolbarOptions = [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'], // toggled buttons
      ['blockquote', 'code-block'],
      [{ 'header': 1 }, { 'header': 2 }], // custom button values
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'script': 'sub' }, { 'script': 'super' }], // superscript/subscript
      [{ 'indent': '-1' }, { 'indent': '+1' }], // outdent/indent
      [{ 'direction': 'rtl' }], // text direction
      [{ 'size': ['small', false, 'large', 'huge'] }], // custom dropdown
      [{ 'color': [] }, { 'background': [] }], // dropdown with defaults from theme
      [{ 'font': [] }],
      [{ 'align': [] }],
      ['link', 'image', 'video'],
      ['clean'] // remove formatting button
    ];
    // QuillJS editor
    var quill = new Quill('#editor', {
      modules: {
        toolbar: toolbarOptions,
        imageUploader: { upload: uploader },
        blotFormatter: {}
      },
      placeholder: 'Write an article...',
      theme: 'snow',
    });
    console.log(quill);
    quill.root.innerHTML = $('#template').html();
    $('#template').remove();

    function uploader(file) {
      return new Promise((resolve, reject) => {
        let formData = new FormData();
        formData.append('myFile', file);
        formData.append('userId', <?= $_SESSION['userId'] ?>);
        formData.append('articleId', <?= $_GET['articleId'] ?>);
        $.ajax({
          type: 'POST',
          url: '<?= BASE_URL ?>/articleEdit/upload_file.php',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          statusCode: {
            200: (filepath) => { resolve('..' + filepath); },
            201: (msg) => {
              reject();
              $('.toast-container').append(`
                    <div id="uploadToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                      <div class="d-flex">
                        <div class="toast-body">Upload Failed: ${msg}</div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                      </div>
                    </div>
                  `);
              $('#uploadToast').on('hidden.bs.toast', function () { $(this).remove(); });
              const toast = new bootstrap.Toast($('#uploadToast'), { delay: 3000 });
              toast.show();
            }
          }
        });
      });
    }


    // save article using AJAX with JQuery
    $('#saveBtn').on('click', function () {
      $.post('<?= BASE_URL . '/articleEdit/save_article.php' ?>', {
        articleId: <?= $article->articleId ?>,
        title: $('#title').val(),
        category: $('#category').val(),
        content: quill.root.innerHTML
      })
        .done(() => {
          $('.toast-container').append(`
              <div id="saveToast" class="toast text-bg-success" role="alert" aria-live="polite" aria-atomic="true">
                <div class="d-flex">
                  <div class="toast-body">Successfully Saved</div>
                  <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
              </div>
            `);
        }).fail(() => {
          $('.toast-container').append(`
              <div id="saveToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                <div class="d-flex">
                  <div class="toast-body">Couldn't Save</div>
                  <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
              </div>
            `);
        }).always(() => {
          $('#saveToast').on('hidden.bs.toast', function () { $(this).remove(); });
          const toast = new bootstrap.Toast($('#saveToast'), { delay: 1000 });
          toast.show();
        });
    });

    $('#publishBtn').on('click', () => { $('#saveBtn').trigger('click') });

  });
</script>

<?php
include 'footer.html';
?>