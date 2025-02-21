var iMaxD = drupalSettings.myConstants.nbmaxD; //Number of AMAPien per Distribution
var iMaxR = drupalSettings.myConstants.nbmaxR; //Number of Réserve per Distribution
var iMaxX = drupalSettings.myConstants.nbmaxX; //Number of Référent per Distribution
var bReferent = document.getElementById("breferentdistrib").value;

function hasChanged(oTemp) {

    sId = oTemp.id;
    iPos = sId.indexOf("][");
    sType = "[" + sId.substring(iPos + 2, iPos + 3).toLowerCase() + "]";

    sIdD = sId.replace(sType, "[d]");
    oD = document.getElementById(sIdD);
    sIdD2 = sId.replace(sType, "[d2]");
    oD2 = document.getElementById(sIdD2);
    sIdD3 = sId.replace(sType, "[d3]");
    oD3 = document.getElementById(sIdD3);
    iD = parseInt(oD2.value);

    sIdX = sId.replace(sType, "[x]");
    oX = document.getElementById(sIdX);
    sIdX2 = sId.replace(sType, "[x2]");
    oX2 = document.getElementById(sIdX2);
    iX = parseInt(oX2.value);

    if (oTemp.checked) {
        //L'utilisateur vient de cocher une boite
        switch (sType) {
            case "[d]":
                oD2.value = iD + 1;
                oX.disabled = true;
                oD3.style.display = 'inline'
                break;
            case "[x]":
                oD.disabled = true;
                oX2.value = iX + 1;
                break;
            default:
                break;
        }
    } else {
        //L'utilisateur vient de décocher une boite
        switch (sType) {
            case "[d]":
                oD2.value = iD - 1;
                if (bReferent == "Y") {
                    oX.disabled = (iX == iMaxX);
                }
                oD3.style.display = 'none'
                break;
            case "[x]":
                oD.disabled = (iD == iMaxD);
                oX2.value = iX - 1;
                break;
            default:
                break;
        }
    }
}
