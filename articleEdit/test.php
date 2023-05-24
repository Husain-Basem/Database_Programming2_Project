<?php
declare(strict_types=1);
include_once '../prelude.php';

$pageTitle = 'Article Edit';
$headerIncludes = '
    <link rel="stylesheet" href="'. BASE_URL . '/css/quill.snow.css" />
    <link rel="stylesheet" href="'. BASE_URL . '/css/quill.imageUploader.min.css" />
    <script src="'. BASE_URL . '/js/quill.min.js"></script>
    <script src="'. BASE_URL . '/js/quill.imageUploader.min.js"></script>
';
include PROJECT_ROOT . '/header.html';

?>

<style>
#output img {
 width: 100%;
}
</style>

<div class="container">
    <div id="editor"></div>
</div>

<script>
$(()=>{
Quill.register('modules/imageUploader', ImageUploader);
    var toolbarOptions = [
      [{
        'header': [1, 2, 3, 4, 5, 6, false]
      }],
      ['bold', 'italic', 'underline', 'strike'], // toggled buttons
      ['blockquote', 'code-block'],

      [{
        'header': 1
      }, {
        'header': 2
      }], // custom button values
      [{
        'list': 'ordered'
      }, {
        'list': 'bullet'
      }],
      [{
        'script': 'sub'
      }, {
        'script': 'super'
      }], // superscript/subscript
      [{
        'indent': '-1'
      }, {
        'indent': '+1'
      }], // outdent/indent
      [{
        'direction': 'rtl'
      }], // text direction

      [{
        'size': ['small', false, 'large', 'huge']
      }], // custom dropdown

      [{
        'color': []
      }, {
        'background': []
      }], // dropdown with defaults from theme
      [{
        'font': []
      }],
      [{
        'align': []
      }],
      ['link', 'image', 'video', 'imageUploader'],

      ['clean'] // remove formatting button
    ];
  var quill = new Quill('#editor', {
    modules: {
      toolbar: toolbarOptions,
      imageUploader: {
        upload: (file) => {
          console.log(file);
          return new Promise((resolve, reject) => {
            let formData = new FormData();
            formData.append('myFile', file);
            $.ajax({
              type: 'POST',
              url: '<?=BASE_URL?>/articleEdit/upload_file.php',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              success: (filepath) => {
                resolve(filepath);
              },
            });
          });
        },
      },
    },
    placeholder: 'Compose an epic...',
    theme: 'snow', // or 'bubble'
  });
});
</script>

<!--
  <script>
$(()=>{

  const stackedit = new Stackedit();
    stackedit.on('fileChange', file => {
      $('#output').html(file.content.html);
    });

    stackedit.openFile({
      name: 'filename', 
      content: { text: $('#textarea').val() }
    },true);

  $('#edit').on('click', ()=> {
    const textArea = $('#textarea');
    const stackedit = new Stackedit();

    stackedit.on('fileChange', file => {
      textArea.val(file.content.text);
      $('#output').html(file.content.html);
    });

    stackedit.openFile({
      name: 'filename', 
      content: { text: textArea.val() }
    });
  });

});
  </script>
-->

<?php
include 'footer.html';
?>
