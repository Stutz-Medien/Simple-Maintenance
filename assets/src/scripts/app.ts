declare const wp: any;

document.addEventListener('DOMContentLoaded', () => {
    const uploadButton = document.querySelector('#upload_logo_button');
    if (uploadButton) {
        uploadButton.addEventListener('click', (e: Event) => {
            e.preventDefault();
            const image = wp
                .media({
                    title: 'Upload Logo',
                    library: { type: 'image' },
                    multiple: false,
                })
                .open()
                .on('select', () => {
                    const uploadedImage = image
                        .state()
                        .get('selection')
                        .first()
                        .toJSON();
                    const imageUrl: string = uploadedImage.url;
                    const logoInput = document.querySelector(
                        '#maintenance_logo'
                    ) as HTMLInputElement;
                    if (logoInput) {
                        logoInput.value = imageUrl;
                    }
                });
        });
    }
});
