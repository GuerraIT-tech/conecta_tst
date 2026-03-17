// Pequena interação: menu responsivo
const toggle = document.querySelector('.nav-toggle')
const nav = document.querySelector('.main-nav')
if(toggle && nav){
  toggle.addEventListener('click', ()=>{
    nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex'
  })
}
