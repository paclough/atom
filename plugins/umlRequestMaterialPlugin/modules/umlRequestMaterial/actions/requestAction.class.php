<?php

class RequestMaterialAction {
	public static function getRequestLink( $resource ) {
        // Collect metadata
        $collectionIdentifier = $resource->getCollectionRoot()->identifier;
        $repositoryCode       = substr($collectionIdentifier, 0, 3);
        // Assign 'ASM' and 'ASU' repository codes to 'ASC' (Special Collections)
        if ( $repositoryCode === 'ASM' || $repositoryCode == 'ASU' ) { $repositoryCode = 'ASC'; }

        $id                   = explode( ":", $resource->getCollectionRoot()->descriptionIdentifier )[ 1 ];
        $title                = $resource->getTitle( array( 'cultureFallback' => true ));
        $creator              = $resource->getCreatorsNameString();
        $collectionId         = $resource->getCollectionRoot()->referenceCode;
        $collectionTitle      = $resource->getCollectionRoot()->title;
        if ( $resource->getDates()[ 0 ] == NULL ) {
          $date               = 'Unknown';
        } else {
          $date               = Qubit::renderDateStartEnd( $resource->getDates()[ 0 ]->getDate( array( 'cultureFallback' => true)), $resource->getDates()[ 0 ]->startDate, $resource->getDates()[ 0 ]->endDate );
        }
        $repository           = $resource->getCollectionRoot()->getRepository();
        $location             = "";
        foreach ( $resource->getPhysicalObjects() as $item ):
          $location .= " " . $item->getLabel();
        endforeach;
        $breadcrumb = $resource;
        $i = $resource;
        while( $i->parent != $resource->getCollectionRoot() && $i != $resource->getCollectionRoot() ) {
          if ( isset( $i->parent )):
            if ( $i->parent == "" ):
              foreach ( $resource->getPhysicalObjects() as $item ):
                $containers .= " " . $item->getLabel();
              endforeach;
              $breadcrumb = "" . ">" . $breadcrumb;
            else:
              $breadcrumb = $i->parent . ">" . $breadcrumb;
            endif;
          endif;
          $i = $i->parent;
        }
        $location = $breadcrumb . ">" . $location;

        // For ARC material create a form to submit a Primo request
        if ( $repository == "Architecture Research Center, University of Miami Libraries" ):
          $request = <<<EOT
<form name="PrimoRequest" target="_blank" method="post" action="https://miami.primo.exlibrisgroup.com/discovery/search?query=holding_call_number,contains,$collectionIdentifier&vid=01UOML_INST:uml_new" style="display: inline">
<input name="SubmitButton" value="Submit request" type="submit" class="btn-request-material">
</form>
EOT;
        else:
        // For all other collections create a form to submit an Aeon request
        // For testing, use the UserReview input so the request isn't delivered via Aeon immediately
        // For production, remove the UserReview input or use an HTML comment
          $request = <<<EOT
<form name='AeonRequest' target='_blank' method = 'post' action='https://aeon.library.miami.edu/aeon/aeon.dll' style='display: inline'>
<input name='AeonForm' value='EADRequest' type='hidden'>
<input name='RequestType' value='Loan' type='hidden'>
<input name='DocumentType' value='Manuscript' type='hidden'>

<div id="$id" style='display: inline'>
<input name="Request" type="hidden" value="$id">
<input value="$collectionTitle" name="ItemTitle_$id" type='hidden'>
<input value="$creator" name="ItemAuthor_$id" type="hidden">
<input value="$date" name="ItemDate_$id" type="hidden">
<input value="$location" name="ItemVolume_$id" type="hidden">
<input value="$repository" name="Location_$id" type="hidden">
<input value="$repositoryCode" name="Site" type="hidden">
<input value="$collectionIdentifier" name="CallNumber_$id" type="hidden">
<!-- <input id="UserReview" name="UserReview" value="Yes" type="checkbox" style="display:none" checked="checked"> -->
</div>

<input name='SubmitButton' value='Submit request' type='submit' class='btn-request-material'>
</form>
EOT;
        endif;

        return $request;
    }
}
