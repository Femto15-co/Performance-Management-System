@include('reports.step2', ['title'=>trans('reports.participate_in_review'), 'buttonText'=>trans('reports.participate'),
'route'=>route('report.putParticipate', $report->id), 'method_field'=>method_field('put')])

