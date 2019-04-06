@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading">Liste des utilisateurs<a href='/users/create'><i class="fa fa-plus"></i></a></div>

                <div class="panel-body">
                    <table class="table table-striped" width="100%">
						<thead>
							<tr>
								<th>Statut</th>
								<th>Nom</th>								
								<th>Rôle</th>
								<th>Action</th>
							</tr>
						</thead>
						
						<tbody>
							@foreach ($users as $user)
							<tr>
								<td><?php if ($user->status==0){echo "<i class='fa fa-ban'></i>";}else{{echo "<i class='fa fa-check'></i>";}}?></td>
								<td><a href='/sites/<?php echo $user->id;?>'>{!! $user->name !!}</a></td>
								<td>
								<?php
								$json= json_decode($user->roles->first());
								if ($json != null){
									echo $json->display_name;
								}
								
								//On enleve la suppression
								//<a href='/users/{!! $user->id !!}/destroy'><i class="fa fa-trash"></i></a>
								?>
								</td>
								<td><a href='/users/{!! $user->id !!}/edit'><i class="fa fa-pencil"></i></a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
					
					<script> 
						//Ajoute le bloc de recherche sur le table
						$(document).ready(function() {
							$(".table").DataTable({
								"paging":   false,
								"info":   false,
								"language": {
									"url": "/js/datatables.french.lang.json"
								},
								"columnDefs": [ {
									  "targets": 'no-search',
									  "searchable": false,
								}],
								"initComplete": function(settings, json) {
									//On pose le focus sur la barre de recherche
									$("input[type='search']").focus();	
								}
							});
						});
					</script> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
