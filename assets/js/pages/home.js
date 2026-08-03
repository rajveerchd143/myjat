document.addEventListener('DOMContentLoaded',function(){
const stats=document.querySelectorAll('.myjat-stat h3');

const observer=new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
if(!entry.isIntersecting)return;

const el=entry.target;
const text=el.textContent;
const end=parseInt(text.replace(/\D/g,''),10)||0;
const suffix=text.replace(/[0-9]/g,'');
let value=0;
const step=Math.max(1,Math.ceil(end/180));
const timer=setInterval(function(){
value+=step;
if(value>=end){
value=end;
clearInterval(timer);
}
el.textContent=value+suffix;
},60);
observer.unobserve(el);
});
}


,{threshold:.0});

stats.forEach(function(stat){
observer.observe(stat);
});

});