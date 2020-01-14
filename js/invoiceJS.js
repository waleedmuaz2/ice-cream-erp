  $(document).on('keyup' , '.p-units' , function(){
    var cTR = $(this).closest('tr');
    if($(this).val() != ''){
        
        $(cTR).find('.p-id').attr('name' , 'product_id[]');
        $(cTR).find('.p-units').attr('name' , 'unit[]');
        $(cTR).find('.p-price').find('input').attr('name' , 'price[]');
        var price = parseFloat($(cTR).find('.p-price').find('input').val());
        var units = parseInt($(this).val());
        $(cTR).find('.row-amount').attr('name' , 'amount[]');
        $(cTR).find('.row-amount').val(price * units);
        $(cTR).find('.show-row-ben').text(parseFloat($(cTR).find('.c-ben').val()) * units);
        var t_amount = 0;var c_ben = 0;
        $('.row-amount').each(function(){
          if($(this).val() != '')
          t_amount = t_amount + parseFloat($(this).val());
        });
        if(t_amount != 0)
        {
            $('#create-section').removeClass('create-invoice-section');
        }
        $('.t-amount').val(t_amount + parseFloat($('#old_balance').val()));
        $('.c-ben').each(function(){
          var ben_u = $(this).closest('tr').find('.p-units').val();
          if(ben_u != '')
          c_ben = c_ben + (parseFloat($(this).val()) * ben_u);
        });
        $('.c-benefit').val(c_ben);
    }
    else{
      $(cTR).find('.p-price').find('input').removeAttr('name');
      $(cTR).find('.p-id').removeAttr('name');
      $(cTR).find('.p-units').removeAttr('name');
      $(cTR).find('.row-amount').val('');
      $(cTR).find('.row-amount').removeAttr('name');
      $(cTR).find('.p-units').val('');
      $(cTR).find('.p-units').removeAttr('name');
      var t_amount = 0;var c_ben = 0;
      $('.row-amount').each(function(){
        if($(this).val() != '')
        t_amount = t_amount + parseFloat($(this).val());
      });
      if(t_amount == 0)
      {
        $('#create-section').addClass('create-invoice-section');
      }
      $('.t-amount').val(t_amount + parseFloat($('#old_balance').val()));
      $(cTR).find('.show-row-ben').text(0);
      $('.c-ben').each(function(){
          var ben_u = $(this).closest('tr').find('.p-units').val();
          if(ben_u != '')
          c_ben = c_ben + (parseFloat($(this).val()) * ben_u);
        });
        $('.c-benefit').val(c_ben);
    }
    $('.sub-total').val(t_amount);
  });
  $('#invoice-form').one('submit' , function(event){
    event.preventDefault();
    $(this).find('.row-amount').removeAttr('disabled');
    $('.advance-amount').removeAttr('disabled');
    $(this).submit();
  });
  $('.r-amount').on('keyup' , function(){
    if($(this).val() == ''){
      var re_amount = 0;
      $(this).val('0');
    }
    else{
      var re_amount = parseFloat($(this).val());
    }
    var total_amount = parseFloat($('.t-amount').val());
    if(total_amount > re_amount){
      $('.amount-left').val(total_amount - re_amount);
      $('.advance-amount').val("0");
    }
    else{
      $('.amount-left').val('0');
      $('.advance-amount').val(re_amount - total_amount);
    }
  });
  $(document).on('change', '.p-price input', function(){
      $(this).attr('value', $(this).val());
  });