<?php $cat_p = []; ?>
<input type="hidden" id="old_balance" value="@if($old_balance == "") 0 @else {{ $old_balance }} @endif" name="old_balance">
@foreach($products as $p)
@if(!in_array($p->category_id , $cat_p))
<tr>
    <td class="bg-info">{{ $p->category->name }}</td>
</tr>
<?php $cat_p[] = $p->category_id ?>
@endif
<tr @if(!in_array($p->id, $shortListed)) class="not-in-sl" @endif>
  <input type="hidden" class="form-control p-id" name="" value="{{ $p->id }}">
    <td>{{ $p->name }}</td>
    <td class="p-price"><input type="text" value="{{ $p->price }}" class="form-control" {{ Auth::user()->role < 3 ? '' : 'readonly' }} /></td>
    <td>
      <input type="number" class="form-control p-units" name="">
    </td>
   <td><input type="number" class="row-amount" disabled=""></td>
</tr>
@endforeach