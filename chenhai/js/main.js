var researchNav = document.getElementById('research'),
	aboutNav = document.getElementById('about');
	
// research nav
researchNav.addEventListener('mouseover', function () {
	researchNav.className = "mouseon";
});

researchNav.addEventListener('mouseout', function () {
	researchNav.className = "";
});

researchNav.addEventListener('click', function () {
	window.location.href = "research.html";
});

// about nav
aboutNav.addEventListener('mouseover', function () {
	aboutNav.className = "mouseon";
});

aboutNav.addEventListener('mouseout', function () {
	aboutNav.className = "";
});

aboutNav.addEventListener('click', function () {
	window.location.href = "about.html";
});

console.log('main.js loaded!')