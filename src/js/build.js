$( ".page-build select" ).change(function(){
	var img = $(".page-build img#" + this.id);
	img.attr("src", "/bundles/pllcore/images/"+this.id+"/"+this.value+".png");
}).change();

