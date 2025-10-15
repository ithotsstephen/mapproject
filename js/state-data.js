// JSON data for each state - you can modify these values as needed
var stateData = {
  "tryjs1": {
    "name": "Jammu and Kashmir",
    "number": 15,
    "population": 12548926,
    "area": 222236
  },
  "tryjs2": {
    "name": "West Bengal", 
    "number": 26,
    "population": 91347736,
    "area": 88752
  },
  "tryjs3": {
    "name": "Uttarakhand",
    "number": 8,
    "population": 10116752,
    "area": 53483
  },
  "tryjs4": {
    "name": "Uttar Pradesh",
    "number": 45,
    "population": 199812341,
    "area": 240928
  },
  "tryjs5": {
    "name": "Tripura",
    "number": 3,
    "population": 3673032,
    "area": 10486
  },
  "tryjs6": {
    "name": "Tamil Nadu",
    "number": 22,
    "population": 72147030,
    "area": 130060
  },
  "tryjs7": {
    "name": "Telangana",
    "number": 18,
    "population": 35003674,
    "area": 112077
  },
  "tryjs8": {
    "name": "Sikkim",
    "number": 2,
    "population": 610577,
    "area": 7096
  },
  "tryjs9": {
    "name": "Rajasthan",
    "number": 35,
    "population": 68548437,
    "area": 342239
  },
  "tryjs10": {
    "name": "Puducherry",
    "number": 1,
    "population": 1247953,
    "area": 479
  },
  "tryjs11": {
    "name": "Punjab",
    "number": 12,
    "population": 27743338,
    "area": 50362
  },
  "tryjs12": {
    "name": "Odisha",
    "number": 20,
    "population": 42037532,
    "area": 155707
  },
  "tryjs13": {
    "name": "Nagaland",
    "number": 4,
    "population": 1978502,
    "area": 16579
  },
  "tryjs14": {
    "name": "Mizoram",
    "number": 3,
    "population": 1097206,
    "area": 21081
  },
  "tryjs15": {
    "name": "Madhya Pradesh",
    "number": 28,
    "population": 72626809,
    "area": 308245
  },
  "tryjs16": {
    "name": "Manipur",
    "number": 5,
    "population": 2855794,
    "area": 22327
  },
  "tryjs17": {
    "name": "Meghalaya",
    "number": 6,
    "population": 2966889,
    "area": 22429
  },
  "tryjs18": {
    "name": "Maharashtra",
    "number": 38,
    "population": 112374333,
    "area": 307713
  },
  "tryjs19": {
    "name": "Lakshadweep",
    "number": 1,
    "population": 64473,
    "area": 32
  },
  "tryjs20": {
    "name": "Kerala",
    "number": 16,
    "population": 33406061,
    "area": 38852
  },
  "tryjs21": {
    "name": "Karnataka",
    "number": 24,
    "population": 61095297,
    "area": 191791
  },
  "tryjs22": {
    "name": "Ladakh",
    "number": 2,
    "population": 274000,
    "area": 59146
  },
  "tryjs23": {
    "name": "Jharkhand",
    "number": 14,
    "population": 32988134,
    "area": 79716
  },
  "tryjs24": {
    "name": "Haryana",
    "number": 11,
    "population": 25351462,
    "area": 44212
  },
  "tryjs25": {
    "name": "Himachal Pradesh",
    "number": 7,
    "population": 6864602,
    "area": 55673
  },
  "tryjs26": {
    "name": "Gujarat",
    "number": 30,
    "population": 60439692,
    "area": 196244
  },
  "tryjs27": {
    "name": "Goa",
    "number": 2,
    "population": 1458545,
    "area": 3702
  },
  "tryjs28": {
    "name": "Dadra and Nagar Haveli",
    "number": 1,
    "population": 585764,
    "area": 491
  },
  "tryjs29": {
    "name": "Delhi",
    "number": 8,
    "population": 16787941,
    "area": 1484
  },
  "tryjs30": {
    "name": "Daman and Diu",
    "number": 1,
    "population": 243247,
    "area": 112
  },
  "tryjs31": {
    "name": "Chhattisgarh",
    "number": 13,
    "population": 25545198,
    "area": 135192
  },
  "tryjs32": {
    "name": "Chandigarh",
    "number": 1,
    "population": 1055450,
    "area": 114
  },
  "tryjs33": {
    "name": "Bihar",
    "number": 25,
    "population": 104099452,
    "area": 94163
  },
  "tryjs34": {
    "name": "Assam",
    "number": 16,
    "population": 31205576,
    "area": 78438
  },
  "tryjs35": {
    "name": "Arunachal Pradesh",
    "number": 4,
    "population": 1383727,
    "area": 83743
  },
  "tryjs36": {
    "name": "Andhra Pradesh",
    "number": 24,
    "population": 49386799,
    "area": 162968
  },
  "tryjs37": {
    "name": "Andaman and Nicobar Islands",
    "number": 1,
    "population": 380581,
    "area": 8249
  }
};

// Function to format numbers with commas
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Function to get formatted data string for a state
function getStateDataString(stateId) {
  var data = stateData[stateId];
  if (!data) return "";
  
  return "<br><strong>Incidents:</strong> " + data.number + 
         "<br><strong>Population:</strong> " + formatNumber(data.population) +
         "<br><strong>Area:</strong> " + formatNumber(data.area) + " km²";
}

// Function to get color based on number value (yellow to red gradient)
function getColorByNumber(number) {
  // Define color ranges: 0-10 (yellow), 11-20 (orange), 21-30 (light red), 31+ (dark red)
  if (number <= 10) {
    // Yellow to light orange (0-10)
    var intensity = number / 10;
    var red = Math.floor(255);
    var green = Math.floor(255 - intensity * 50); // 255 to 205
    var blue = Math.floor(0);
    return "rgb(" + red + "," + green + "," + blue + ")";
  } else if (number <= 20) {
    // Light orange to orange (11-20)
    var intensity = (number - 10) / 10;
    var red = Math.floor(255);
    var green = Math.floor(205 - intensity * 80); // 205 to 125
    var blue = Math.floor(0);
    return "rgb(" + red + "," + green + "," + blue + ")";
  } else if (number <= 30) {
    // Orange to light red (21-30)
    var intensity = (number - 20) / 10;
    var red = Math.floor(255);
    var green = Math.floor(125 - intensity * 85); // 125 to 40
    var blue = Math.floor(0);
    return "rgb(" + red + "," + green + "," + blue + ")";
  } else {
    // Light red to dark red (31+)
    var intensity = Math.min((number - 30) / 20, 1); // Cap at intensity 1
    var red = Math.floor(255 - intensity * 55); // 255 to 200
    var green = Math.floor(40 - intensity * 40); // 40 to 0
    var blue = Math.floor(0);
    return "rgb(" + red + "," + green + "," + blue + ")";
  }
}

// Function to populate state labels dynamically
function populateStateLabels() {
  // Loop through all states and populate their labels
  for (var stateId in stateData) {
    if (stateData.hasOwnProperty(stateId)) {
      var labelElement = document.getElementById('label-' + stateId);
      if (labelElement && stateData[stateId]) {
        labelElement.textContent = stateData[stateId].number;
      }
    }
  }
}

// Function to apply colors to states based on their number values
function applyStateColors() {
  for (var stateId in stateData) {
    if (stateData.hasOwnProperty(stateId)) {
      var stateElement = document.getElementById(stateId);
      if (stateElement && stateData[stateId]) {
        var color = getColorByNumber(stateData[stateId].number);
        stateElement.setAttribute('fill', color);
        
        // Also update the config colors if they exist
        if (typeof tryjsconfig !== 'undefined' && tryjsconfig[stateId]) {
          tryjsconfig[stateId].upColor = color;
          // Keep interactive colors slightly different
          tryjsconfig[stateId].overColor = color.replace('rgb', 'rgba').replace(')', ', 0.8)');
          tryjsconfig[stateId].downColor = color.replace('rgb', 'rgba').replace(')', ', 0.6)');
        }
      }
    }
  }
}

// Call the functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  populateStateLabels();
  applyStateColors();
});