<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/declarer.css">
</head>
<body>

<form id="rendered-form">
  <div class="rendered-form">
    <div class="formbuilder-number form-group field-txt_age">
      <label for="txt_age" class="formbuilder-number-label">Que vous est-il arrivé ? <span class="formbuilder-required">*</span></label><br>
      <input type="text" placeholder="un accident de voiture" class="form-control" name="txt_age" min="0" max="130" step="1" id="txt_age" required="required" aria-required="true">
    </div>
    <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> veuillez indiquez l'endroit de l'accident ! <span class="formbuilder-required">*</span></label><br>
      <input type="text" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>

      <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> veuillez indiquez la date de l'accident ! <span class="formbuilder-required">*</span></label><br>
      <input type="date" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>

      <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> Veuillez indiquer la date de déclaration ! <span class="formbuilder-required">*</span></label><br>
      <input type="date" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>


          <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Combien de temps s'est écoulé depuis l'accident ? <span class="formbuilder-required">*</span></label><br>
      <input type="time" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>


    <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> De combien avez vous besoin ? <span class="formbuilder-required">*</span></label><br>
      <input type="number" placeholder="500000" class="form-control" name="txt_postalcode" id="txt_postalcode" >
    </div>

    <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Expliquez les faits !<span class="formbuilder-required">*</span></label><br>
      <textarea type="number" placeholder="Explique vous ici....." class="form-control" name="txt_postalcode" id="txt_postalcode" style="padding-bottom: 90px;"></textarea>
    </div>

        <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Envoyez des preuves<span class="formbuilder-required">*</span></label><br>
      <input type="file" placeholder="Explique vous ici....." class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>


        <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Soumettez le Rapport de police si possible ??<span class="formbuilder-required">*</span></label><br>
      <input type="file" class="form-control" name="txt_postalcode" id="txt_postalcode" >
    </div>


    <div class="formbuilder-checkbox-group form-group field-chk_legalage">
      <label for="chk_legalage" class="formbuilder-checkbox-group-label"> y'avais t'il des Témoins ?</label><br>
      <div class="checkbox-group">
        <div class="formbuilder-checkbox">
          <input name="chk_legalage[]" id="chk_legalage-0" class="form-checkbox" aria-required="true" type="checkbox" required="required" placeholder="">
          Oui
          <input name="chk_legalage[]" id="chk_legalage-0" class="form-checkbox" aria-required="true" type="checkbox" required="required" placeholder="">
          NOn
        </div>
      </div>
    </div>
    <div class="formbuilder-button form-group field-btn_submit">
      <button type="submit" class="btn-default btn btn_submit" name="btn_submit" value="true" style="default" id="btn_submit">Declarer</button>
    </div>
  </div>
</form>
    
</body>
</html>