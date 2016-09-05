function Participants_points_form() 
{
    this.share = $("#share").val();
    this.point_info_focusout = function() {
        var that = this;
        $(".points_info").focusout(function() {
            
            var td = $(this).parents("td");
            that.end_edit_mode(td);
            
        });
    }
    
    this.save_pount_info = function(data) {
        
        $.get("/panel/Shares/save_point_info",data, function() {
            
        });
    }
    
    this.points_link_click = function() {
        var that = this;
        $("a.points_link").click(function(event) {
            
            event.preventDefault();
            if ($(".points_info.edit_mode").size() == 1) {

                var td_edit = $(".points_info.edit_mode").parents("td");
                that.end_edit_mode(td_edit);

                
                
                
            }
            
            var td = $(this).parents("td");
            that.start_edit_mode(td);

            
        });
    }
    this.start_edit_mode = function(td) {
        var input = td.find("input");
        var a = td.find("a.points_link");
        a.hide();
        input.show();
        input.addClass("edit_mode");
        
    }
    this.end_edit_mode = function (td) {
        var input = td.find("input");
        var a = td.find("a.points_link");
        var id = input.data("pid");
        var value = input.val();
        var data = {shares_participant_id: id, points: value, share_id: this.share};
        console.log(data);
        this.save_pount_info(data);

    }
    
    this.save = function() {
        
        var that = this;
        $("#saves_points").click(function(event) {
            event.preventDefault();
            
            $(".points_info").each(function (i,pi) {

                var td = $(pi).parents("td");
                that.end_edit_mode(td);

            });
        });
        
    }
    
    this.init = function() {
        var share = $("#share").val();
        $("a.points_link").tooltip({"html":"111"});
        this.save();
        //this.points_link_click();
       // this.point_info_focusout();
    }
}

$(document).ready(function() {
    
    var participants_points_form = new Participants_points_form();

    participants_points_form.init();
    
    
});