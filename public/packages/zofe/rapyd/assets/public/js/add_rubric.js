$(document).ready(function() {
    
    $("#div_generate_alias").click(function() {
        
        $.get("/content/translit_alias/"+$("#name").val(),function(alias) {
            $("#alias").val(alias);
            
        })
    });
    $("#div_add_rubric_button .btn").click(function() {
        var val = $("#add_rubric").val();
        if (val == "") {
            alert("Введите рубрику");
            return;
        }


        if (hasValue(val))
        {
            alert("такая рубрика уже существует");
            return;
        } 
        
        addValue(val);
        
             
            
             
         
    });
    
    function hasValue(value) {
        
        var has_value = false;
         $("#div_rubric input").each(function(i,input) {
             if( $(input).val() === value ) {
                 has_value = true;
                 return;
                // 
             }
             
         });
        return has_value;
        
    }
    function addValue(val) {
      var input = '<input name="rubric[]" type="checkbox" value="'+val+'">'+val+'&nbsp;&nbsp;'; 
      $("#div_rubric").append( input );
    }
});