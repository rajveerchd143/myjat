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
