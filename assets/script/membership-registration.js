document.addEventListener("DOMContentLoaded", function () {

    const photo = document.getElementById("photo");
    const preview = document.getElementById("myjat-photo-preview");

    if(photo){

        photo.addEventListener("change", function(){

            const file = this.files[0];

            if(!file){
                preview.style.display="none";
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e){

                preview.src = e.target.result;
                preview.style.display = "block";

            };

            reader.readAsDataURL(file);

        });

    }

});