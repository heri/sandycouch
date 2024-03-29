<?php
/**
* Places view
*
* @package places
* @author The myTravelbook Team <http://www.sourceforge.net/projects/mytravelbook>
* @copyright Copyright (c) 2005-2006, myTravelbook Team
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
* @version $Id$
*/

class PlacesView extends PAppView {
    private $_model;
    
    public function __construct(Places $model) {
        $this->_model = $model;
    }

    // only for testing
    public function testpage() {
        require 'templates/testPage.php';
    }   
    // only for testing // END
    public function customStyles()
    {       
    // calls a 1column layout 
         echo "<link rel=\"stylesheet\" href=\"styles/css/minimal/screen/custom/places.css\" type=\"text/css\"/>";
    }
    public function teaserplaces($countrycode,$country,$region,$city) {
        require 'templates/teaserCountry.php';
    }
    public function submenu($subTab) {
        //require 'templates/submenu.php';
    }
    public function displayPlacesInfo($countryinfo, $members) {
        $memberlist = $this->generateMemberList($members);
        $forums = '';
        $wiki = new WikiController();
        $wikipage = str_replace(' ', '', ucwords($countryinfo->name));
        require 'templates/countryInfo.php';
    }
    public function displayRegionInfo($regioninfo, $members) {
        //$memberlist = $this->generateMemberList($members);
        $forums = '';
        $wiki = new WikiController();
        $wikipage = str_replace(' ', '', ucwords($regioninfo->region));
        require 'templates/regionInfo.php';
    }
    public function displayCityInfo($cityinfo, $members) {
        $forums = '';
        $wiki = new WikiController();
        $wikipage = str_replace(' ', '', ucwords($cityinfo->city));
        require 'templates/cityInfo.php';
    }
    private function generateMemberList($members) {
			return($members) ;
    }

    public function displayPlacesOverview($allcountries) {
        $words = new MOD_words();
        $countrylist = '<table><tr>';
        $countrylist .= '<td style="vertical-align: top;"><h3>'.$words->getformatted('Africa').'</h3>'.$this->displayContinent('AF', $allcountries['AF']).'</td>';
        $countrylist .= '<td style="vertical-align: top;"><h3>'.$words->getformatted('Asia').'</h3>'.$this->displayContinent('AS', $allcountries['AS']).'</td>';
        $countrylist .= '<td style="vertical-align: top;"><h3>'.$words->getformatted('Europe').'</h3>'.$this->displayContinent('EU', $allcountries['EU']).'</td>';
        $countrylist .= '<td style="vertical-align: top;"><h3>'.$words->getformatted('NorthAmerica').'</h3>'.$this->displayContinent('NA', $allcountries['NA']);
        $countrylist .= '<h3>'.$words->getformatted('SouthAmerica').'</h3>'.$this->displayContinent('SA', $allcountries['SA']).'</td>';
        $countrylist .= '<td style="vertical-align: top;"><h3>'.$words->getformatted('Oceania').'</h3>'.$this->displayContinent('OC', $allcountries['OC']).'</td>';
//      $countrylist .= $this->displayContinent('AN', $allcountries['AN']).'</td>';
        
        $countrylist .= '</tr></table>';
    
        require 'templates/countryOverview.php';
    }
    
    public function displayRegions($regions,$countrycode) {
        $regionlist = '<div class="floatbox places">';
        $regionlist .= '<ul class="float_left">';
        $ii = 0;
        foreach ($regions as $region) {
            $ii++;
            if ($ii > 10) {
                $regionlist .= '</ul>';
                $regionlist .= '<ul class="float_left">';
                $ii = 0;
            }
            $regionlist .= '<li><a class="highlighted" href="places/'.$countrycode.'/'.$region['name'].'">'.$region['name'].' <span class="small grey">('.$region['number'].')</span>';
            $regionlist .= '</a></li>';
        }
        $regionlist .= '</ul>';
        $regionlist .= '</div>';

        require 'templates/regionOverview.php';
    }   
    
    public function displayCities($cities,$region,$countrycode) {
        $citylist = '<ul>';
        
        foreach ($cities as $city) {
            $citylist .= '<li><a class="highlighted" href="places/'.$countrycode.'/'.$region.'/'.$city->city.'">'.$city->city.' <span class="small grey">('.$city->NbMember.')</span>';
            $citylist .= '</a></li>';
        }
        $citylist .= '</ul>';        
    
        require 'templates/cityOverview.php';
    }   

    private function displayContinent($continent, $countries) {
        $html = '';
        $html .= '<ul>';
        foreach ($countries as $code => $country) {
           $html .= '<li class="spritecontainer"><div class="sprite sprite-'.strtolower($code).'"><a href="places/'.$code.'"></a></div> <a href="places/'.$code.'" class="'.($country['number'] ? 'highlighted' : 'grey').'">'.$country['name'];
            if ($country['number']) {
               $html .= '<span class="small grey"> ('.$country['number'].')</span>';
            }
            $html .= '</a></li>';
        }
        $html .= '</ul>';
        return $html;   
    }
    
    public function placesNotFound($ss="") {
        echo '<h2>Places '.$ss.' not found</h2>'; // TODO
    }
}
?>
