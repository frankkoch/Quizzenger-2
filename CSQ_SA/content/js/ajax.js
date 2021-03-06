function chooseOnlyCategory(selection, element,catname){
	document.getElementById("opquestion_form_chosenCategory").value=selection;
	setActiveElement(element);
	document.getElementById("opquestion_form_chosenCategoryName").value=catname;
    $(".opquestion_form").bootstrapValidator('revalidateField', 'opquestion_form_chosenCategory');

    document.getElementById("createNewCategoryInList").className="list-group-item";
    className = "list-group-item";
    $(".opquestion_form").bootstrapValidator('revalidateField', 'opquestion_form_chosenCategoryName');
}

function setActiveElement(element){
	var list = element.parentNode.getElementsByTagName('a');
	for (var index = 0; index < list.length; ++index) {
		list[index].className = "list-group-item";
	}
	element.className = element.className + " active";
}



function setParentCategory(id){
	var parentIdField = document.getElementById("opquestion_form_chosenCategory_parent_id");
	if (typeof(parentIdField) != 'undefined' && parentIdField != null)
	{
		parentIdField.value=id;
	}
}

function createNewCategoryInList(element){
	setActiveElement(element);
	document.getElementById("opquestion_form_chosenCategoryName").value=document.getElementById("categorylist_ajax_new_category").value;
	document.getElementById("opquestion_form_chosenCategory").value=-1;
    $(".opquestion_form").bootstrapValidator('revalidateField', 'opquestion_form_chosenCategoryName');
}

function radioButtonSelected(index){
	document.getElementById("opquestion_form_chosenCorrectAnswer").value=index;
    $(".opquestion_form").bootstrapValidator('revalidateField', 'opquestion_form_chosenCorrectAnswer');
}



function showCategories(element, parentId, containerId, mode) {
	mode = typeof mode !== 'undefined' ? mode : false;
	var submitButton=document.getElementById("submit_opquestion_btn");
	if(!submitButton==null){
		submitButton.disabled = true;
	}
	var xmlhttp;
	setActiveElement(element);
	if (parentId == "") {
		document.getElementById(containerId).innerHTML = "";
		return;
	}
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById(containerId).innerHTML = xmlhttp.responseText;
			if(containerId == 'categories'){
				document.getElementById('subcategories').innerHTML = '';
			}
		}
	}
	if(mode === false){
		xmlhttp.open("GET", "index.php?view=categorylist_ajax&type=ajax&id=" + parentId + "&container=" + containerId, true);
	} else if(mode=='add_question') {
		xmlhttp.open("GET", "index.php?view=categorylist_ajax&type=ajax&mode=add_question&id=" + parentId + "&container=" + containerId, true);
	} else if(mode=='generator'){
		xmlhttp.open("GET", "index.php?view=categorylist_ajax&type=ajax&mode=generator&id=" + parentId + "&container=" + containerId, true);
	}
	xmlhttp.send();
}

function newRating(question_id){
	var comment;
	comment = document.getElementById('rating').value;
	var stars = document.querySelector('input[name = "rating"]:checked').value;

	var parent = null;

	//ajaxGET("index.php?view=addrating_ajax&type=ajax&question_id="+question_id+"&stars="+stars+"&comment="+comment+"&parent="+parent);
	ajaxGetJson("index.php?view=addrating_ajax&type=ajax&question_id="+question_id+"&stars="+stars+"&comment="+comment+"&parent="+parent, 
		function(){ location.reload(true);}, 
		function(){ location.reload(true);}
	);

	var child = document.getElementById('ratingdiv');
	document.getElementById('ratingFormButton').disabled = true;
	document.getElementById('ratingFormButton').innerHTML= 'Bewertung abgeschickt';
}

function newComment(question_id,parent){
	var comment;
	comment = document.getElementById('comment'+parent).value;
	stars = null;

	ajaxGET("index.php?view=addrating_ajax&type=ajax&question_id="+question_id+"&stars="+stars+"&comment="+comment+"&parent="+parent);
	var child = document.getElementById('commentdiv'+parent);
	document.getElementById('commentFormButton'+parent).disabled = true;
	document.getElementById('commentFormButton'+parent).innerHTML= 'Kommentar abgeschickt';
}

function deleteQuestionFromQuiz(quiz, question, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_quizquestion&type=ajax&question=" + question + "&quiz=" + quiz +"", onSuccess, onError);
}
function deleteQuestion(question, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_question&type=ajax&question=" + question+"", onSuccess, onError);
}
function deleteGame(gameid, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_game&type=ajax&gameid=" + gameid+"", onSuccess, onError);
}
function deleteQuiz(quiz, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_quiz&type=ajax&quiz=" + quiz +"", onSuccess, onError);
}
function deleteRating(rating, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_rating&type=ajax&rating=" + rating +"", onSuccess, onError);
}

function ajaxGET(url){
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

function ajaxGetJson(url, onSuccess, onError){
	$.ajax({
		url: url,
		type: "GET",
		contentType: false,
		cache: false,
		processData:false,
		complete: function(response){
			if(response.responseJSON === undefined || response.responseJSON.data === undefined
				|| response.responseJSON.result != 'success'){
				if(onError !== undefined) onError();
				return;
			}
			if(onSuccess !== undefined) onSuccess();
		}
	});
}

function removeSubCat(id, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_sub_cat&type=ajax&id=" + id, onSuccess, onError);
}

function inactiveUser(id, onSuccess, onError){
	ajaxGetJson("index.php?view=inactive_user&type=ajax&id=" + id, onSuccess, onError);
}

function deleteReports(id, type, onSuccess, onError){
	ajaxGetJson("index.php?view=remove_reports&type=ajax&id=" + id +"&reporttype=" + type, onSuccess, onError);
}
function setWeight(weight, id){
	ajaxGetJson("index.php?view=set_weight&type=ajax&id=" + id + "&weight=" + weight +"",
		function(){ document.getElementById('dropdownWeight' + id).innerHTML = weight + " <span class=\"caret\"></span>"; });
}

function getReports(object_id, type){
	var xmlhttp;
	var table;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

			var oTable = $('#tableListOfReports').dataTable();
			oTable.fnDestroy();

			document.getElementById('tablebodyreports').innerHTML = xmlhttp.responseText;

			$('#tableListOfReports').dataTable({
				responsive: true,
		        "order": [[ 1, "desc" ]]
			});
		}
	}
	xmlhttp.open("GET", "index.php?view=report_list&type=ajax&id=" + object_id +"&reporttype=" + type, true);
	xmlhttp.send();
}