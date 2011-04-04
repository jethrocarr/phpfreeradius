/*
	include/javascript/nasdevices.js

	Javascript for working with the nas devices UI.
*/

var num_stationids;



/*
	Page Load Functions
*/
$(document).ready(function()
{
	/*
		Load the current number of configured Called Station ID attributes
	*/
	num_stationids = $("input[name='num_stationids']").val();


	/*
	 *	Execute row addition upon link/button click
	 */
	$(".add_stationid").live("click", function(){
		var cell = $(this).parent();
		add_stationid_row(cell);
		return false;
	});


	/*
	 * 	Delete row upon link/button click
	 */
	$(".delete_undo").live("click", function(){
		var cell = $(this).parent();
		delete_undo_row(cell);
		return false;
	});
	
	$(".delete_undo").live("select", function(){
		var cell = $(this).parent();
		delete_undo_row(cell);
		return false;
	});
	
});


/*
	add_stationid_row

	Copys the original station ID row and creates a new one on the table.
*/
function add_stationid_row()
{
	// we need to know the name of the previous row in order to clone
	num_stationids_prev = num_stationids;
	num_stationids++;

	// clone the previous form row
	previous_row		= $("input[name='nas_station_" + (num_stationids_prev) + "_stationid']").parent().parent();
	new_row			= $(previous_row).clone().insertAfter(previous_row);

	// adjust names of form elements and values.
	$(new_row).children().children("input[name='nas_station_" + (num_stationids_prev) + "_stationid']").removeAttr("name").attr("name", "nas_station_" + num_stationids + "_stationid").val("");
	$(new_row).children().children("select[name='nas_station_" + (num_stationids_prev) + "_ldapgroup']").removeAttr("name").attr("name", "nas_station_" + num_stationids + "_ldapgroup").val("");
	$(new_row).children().children("input[name^='nas_station_" + (num_stationids_prev) + "_delete_undo']").removeAttr("name").attr("name", "nas_station_" + num_stationids + "_delete_undo").val("false");

	// update station id count
	$("input[name='num_stationids']").val(num_stationids);
}


/*
  	delete_undo_row
  
  	Fades in/out when deleting/undoing a row and flags as such for the processing logic.
*/
function delete_undo_row(cell)
{
	var status = $(cell).children("input").val();
	if (status == "false")
	{
		$(cell).siblings().fadeTo("slow", 0.1);
		$(cell).children(".delete_undo").children().html("undo");
		$(cell).children("input").val("true");
	}
	else if (status == "true")
	{
		$(cell).siblings().fadeTo("slow", 1);
		$(cell).children(".delete_undo").children().html("delete");
		$(cell).children("input").val("false");
	}
}


