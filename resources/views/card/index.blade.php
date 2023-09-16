@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading"><a href='/' ><img src="/images/favicon/favicon-32x32.png" /></a>&nbsp;&nbsp;Liste des cartes<a href='/cards/create?game_id=<?php echo $game_id;?>'><i class="fa fa-plus"></i></a></div>

                <div class="panel-body">
					<select class="form-control" name="id_game" onchange="window.location.href='/cards?game_id='+this.value">
						<option value="0"></option>
						<?php
						foreach ($games as $game){
							?>
							<option <?php if ($game->id==$game_id) {echo "selected";}?> value="<?php echo $game->id;?>"><?php echo $game->name;?></option>
							<?php
						}
						?>
					</select>
                    <table class="table table-striped" width="100%">
						<thead>
							<tr>
								<th>Jeux</th>
								<th>Statut</th>
								<th>Nom</th>
								<th>Action</th>
							</tr>
						</thead>

						<tbody>
							@foreach ($cards as $card)
							<?php
							if ($card->game_id==$game_id or $game_id==0){?>
								<tr>
									<td><?php echo $games[$card->game_id]->name;?></td>
									<td><?php if ($card->status==0){echo "<i class='fa fa-ban'></i>";}else{{echo "<i class='fa fa-check'></i>";}}?></td>
									<td><a href='/cards/<?php echo $card->id;?>'>{!! $card->name !!}</a></td>

									<td><a href='/cards/{!! $card->id !!}/edit'><i class="fa fa-pencil"></i></a>
                                        &nbsp;&nbsp;
                                        <form class="trashform" action="{{route('cards.destroy', $card->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="trash"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
								</tr>
							<?php
							}
							?>
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
