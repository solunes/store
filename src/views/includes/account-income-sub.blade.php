@foreach($items as $concept_name => $concept)
  @if($concept_name!='total')
    <tr>
      <td><a target="_blank" href="{{ url('admin/account-book-detail?initial_date='.$initial_date.'&initial_date_submit=&end_date='.$end_date.'&end_date_submit=&currency_id=1&place_id='.$place.'&account_id='.$concept['id']) }}">{{ $concept_name }}</a></td>
      <td>{{ $concept['total'].' '.$currency->name }}</td>
    </tr>
  @endif
@endforeach