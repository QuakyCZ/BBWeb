$(document).ready(function() {
    $('.markdown-editor').each((id, input) =>
    {
        console.log(input);
        let mde = new EasyMDE({
            element: input,
            spellChecker: false,
            autosave: {
                enabled: true,
                uniqueId: input.id,
            },
            uploadImage: true,
            toolbar: [
                "bold", "italic", "heading", "|",
                "quote", "unordered-list", "ordered-list", "|",
                "link", "image", "upload-image", "|",
                "preview", "side-by-side", "fullscreen", "guide", "|",
                "undo", "redo"
            ]
        });

        mde.codemirror.on('change', () =>
        {
            input.val(mde.value());
        });
    });
});