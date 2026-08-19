document.addEventListener("DOMContentLoaded",()=>{

const glow=document.createElement("div");
glow.className="myjat-mouse-glow";
document.body.appendChild(glow);

let x=window.innerWidth/2;
let y=window.innerHeight/2;
let tx=x;
let ty=y;

document.addEventListener("mousemove",e=>{
tx=e.clientX;
ty=e.clientY;
});

function animate(){
x+=(tx-x)*0.08;
y+=(ty-y)*0.08;
glow.style.transform=`translate(${x-250}px,${y-250}px)`;

requestAnimationFrame(animate);
}

animate();

const leaves=document.createElement("div");
leaves.className="myjat-leaves";

for(let i=0;i<6;i++){
const leaf=document.createElement("div");
leaf.className="myjat-leaf";
leaf.style.backgroundImage="url('/wp-content/themes/kleo-child/assets/images/background/leaf-1.png')";
if(i%2){
leaf.style.backgroundImage="url('/wp-content/themes/kleo-child/assets/images/background/leaf-2.png')";
}
leaves.appendChild(leaf);
}


const canvas=document.createElement("div");
canvas.className="myjat-site-bg";

document.body.prepend(canvas);
document.body.prepend(leaves);


const rays=document.createElement("div");
rays.className="myjat-light-rays";

for(let i=0;i<3;i++){

const light=document.createElement("div");
light.className="myjat-light";
rays.appendChild(light);

}
document.body.prepend(rays);


});


/*==================================================
   MYJAT Background Slideshow
==================================================*/

document.addEventListener('DOMContentLoaded',function(){

const section=document.querySelector('section.container-wrap.main-color');

if(!section)return;

const images=[
'/wp-content/themes/kleo-child/assets/images/background/back-1.webp',
'/wp-content/themes/kleo-child/assets/images/background/back-2.webp',
'/wp-content/themes/kleo-child/assets/images/background/back-3.webp',
'/wp-content/themes/kleo-child/assets/images/background/back-4.webp',
'/wp-content/themes/kleo-child/assets/images/background/back-5.webp'
];

let current=0;

section.style.setProperty('--myjat-bg-current',`url("${images[current]}")`);
section.style.setProperty('--myjat-bg-next',`url("${images[1]}")`);

images.forEach(function(src){
const img=new Image();
img.src=src;
});

function changeBackground(){

const next=(current+1)%images.length;

section.style.setProperty('--myjat-bg-next',`url("${images[next]}")`);

section.classList.add('myjat-bg-fade');

setTimeout(function(){

section.style.setProperty('--myjat-bg-current',`url("${images[next]}")`);

section.classList.remove('myjat-bg-fade');

current=next;

},3000);

}

setInterval(changeBackground,9000);

});