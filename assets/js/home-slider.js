document.addEventListener('DOMContentLoaded',function(){

const slider=document.querySelector('.myjat-slider-box');

if(!slider)return;

const slides=slider.querySelectorAll('.myjat-slider-item');
const dots=document.querySelectorAll('.myjat-slider-dots span');
const prev=slider.querySelector('.myjat-slider-prev');
const next=slider.querySelector('.myjat-slider-next');

if(!slides.length)return;

let current=0;
let timer=null;
let touchStartX=0;
let touchEndX=0;
let isTouching=false;

function showSlide(index){

    if(index<0){
        index=slides.length-1;
    }

    if(index>=slides.length){
        index=0;
    }

    slides.forEach((slide,i)=>{
        const active=i===index;

        slide.classList.toggle('active',active);
        slide.setAttribute('aria-hidden',active?'false':'true');
    });

    dots.forEach((dot,i)=>{
        const active=i===index;

        dot.classList.toggle('active',active);
        dot.setAttribute('aria-current',active?'true':'false');
    });

    current=index;
}

function nextSlide(){
    showSlide(current+1);
}

function prevSlide(){
    showSlide(current-1);
}

function startAutoSlide(){

    clearInterval(timer);

    timer=setInterval(()=>{
        nextSlide();
    },6000);
}

function stopAutoSlide(){

    clearInterval(timer);
    timer=null;
}

function restartAutoSlide(){

    stopAutoSlide();
    startAutoSlide();
}

next.addEventListener('click',function(){

    nextSlide();
    restartAutoSlide();

});

prev.addEventListener('click',function(){

    prevSlide();
    restartAutoSlide();

});

dots.forEach((dot,index)=>{

    dot.setAttribute('role','button');
    dot.setAttribute('tabindex','0');
    dot.setAttribute('aria-label','Slide '+(index+1));

    dot.addEventListener('click',function(){

        showSlide(index);
        restartAutoSlide();

    });

    dot.addEventListener('keydown',function(event){

        if(event.key==='Enter' || event.key===' '){

            event.preventDefault();

            showSlide(index);
            restartAutoSlide();

        }

    });

});

slider.addEventListener('mouseenter',function(){

    stopAutoSlide();

});

slider.addEventListener('mouseleave',function(){

    if(!isTouching){
        startAutoSlide();
    }

});

slider.addEventListener('focusin',function(){

    stopAutoSlide();

});

slider.addEventListener('focusout',function(event){

    if(!slider.contains(event.relatedTarget)){
        startAutoSlide();
    }

});

slider.addEventListener('touchstart',function(event){

    touchStartX=event.changedTouches[0].screenX;
    isTouching=true;
    stopAutoSlide();

},{passive:true});

slider.addEventListener('touchend',function(event){

    touchEndX=event.changedTouches[0].screenX;

    const distance=touchEndX-touchStartX;

    if(Math.abs(distance)>50){

        if(distance<0){
            nextSlide();
        }else{
            prevSlide();
        }

    }

    isTouching=false;
    startAutoSlide();

},{passive:true});

document.addEventListener('keydown',function(event){

    if(!slider.matches(':hover') && !slider.contains(document.activeElement)){
        return;
    }

    if(event.key==='ArrowLeft'){

        prevSlide();
        restartAutoSlide();

    }

    if(event.key==='ArrowRight'){

        nextSlide();
        restartAutoSlide();

    }

});

showSlide(0);
startAutoSlide();




});