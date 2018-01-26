if ((typeof StoreAPI) === 'undefined') {
  StoreAPI = {};
}

StoreAPI.cultures = [
  {
    code: 'vi-VN',
    thousands: ',',
    decimal: '.',
    numberdecimal: 0,
    money_format: '{{amount}}'
  }, {
    code: 'en-US',
    thousands: ',',
    decimal: '.',
    numberdecimal: 2,
    money_format: '{{amount}}'
  }
]
StoreAPI.getCulture = function(code) {
  var culture;
  for (n = 0; n < StoreAPI.cultures.length; n++) {
    if (StoreAPI.cultures[n].code == code) {
      culture = StoreAPI.cultures[n];
      break;
    }
  }
  if (!culture) {
    culture = StoreAPI.cultures[0];
  }
  return culture;
}
StoreAPI.format = StoreAPI.getCulture(StoreAPI.culture)
StoreAPI.money_format = "{{amount}}";
StoreAPI.onError = function(XMLHttpRequest, textStatus) {
  var data = eval('(' + XMLHttpRequest.responseText + ')');
  if (!!data.message) {
    alert(data.message + '(' + data.status + '): ' + data.description);
  } else {
    alert('Error : ' + StoreAPI.fullMessagesFromErrors(data).join('; ') + '.');
  }
};
StoreAPI.fullMessagesFromErrors = function(errors) {
  var fullMessages = [];
  jQuery.each(errors, function(attribute, messages) {
    jQuery.each(messages, function(index, message) {
      fullMessages.push(attribute + ' ' + message);
    });
  });
  return fullMessages
}
StoreAPI.onCartUpdate = function(cart) {
  // alert('There are now ' + cart.item_count + ' items in the cart.');
};
StoreAPI.onCartShippingRatesUpdate = function(rates, shipping_address) {
  var readable_address = '';
  if (shipping_address.zip)
    readable_address += shipping_address.zip + ', ';
  if (shipping_address.province)
    readable_address += shipping_address.province + ', ';
  readable_address += shipping_address.country
  alert('There are ' + rates.length + ' shipping rates available for ' + readable_address + ', starting at ' + StoreAPI.formatMoney(rates[0].price) + '.');
};
StoreAPI.onItemAdded = function(line_item) {
  alert(line_item.title + ' was added to your shopping cart.');
};
StoreAPI.onProduct = function(product) {
  alert('Received everything we ever wanted to know about ' + product.title);
};
StoreAPI.formatMoney = function(cents, format) {
  cents = cents / 100;
  if (typeof cents == 'string')
    cents = cents.replace(StoreAPI.format.thousands, '');
  var value = '';
  var patt = /\{\{\s*(\w+)\s*\}\}/;
  var formatString = (format || this.money_format);
  function addCommas(moneyString) {
    return moneyString.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1' + StoreAPI.format.thousands);
  }
  switch (formatString.match(patt)[1]) {
    case 'amount':
      value = addCommas(floatToString(cents, StoreAPI.format.numberdecimal));
      break;
    case 'amount_no_decimals':
      value = addCommas(floatToString(cents, 0));
      break;
    case 'amount_with_comma_separator':
      value = floatToString(cents, StoreAPI.format.numberdecimal).replace(/\./, ',');
      break;
    case 'amount_no_decimals_with_comma_separator':
      value = addCommas(floatToString(cents, 0)).replace(/\./, ',');
      break;
  }
  return formatString.replace(patt, value);
};
StoreAPI.resizeImage = function(image, size) {
  try {
    if (size == 'original') {
      return image;
    } else {
      var matches = image.match(/(.*\/[\w\-\_\.]+)\.(\w{2,4})/);
      return matches[1].replace(/http:/g, '') + '_' + size + '.' + matches[2];
    }
  } catch (e) {
    return image.replace(/http:/g, '');
  }
};
StoreAPI.addItem = function(variant_id, quantity, callback) {
  var quantity = quantity || 1;
  var params = {
    type: 'POST',
    url: '/cart/add',
    data: 'quantity=' + quantity + '&id=' + variant_id,
    dataType: 'json',
    success: function(line_item) {
      if ((typeof callback) === 'function') {
        callback(line_item);
      } else {
        StoreAPI.onItemAdded(line_item);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.addItemFromForm = function(form_id, callback) {
  var params = {
    type: 'POST',
    url: '/cart/add',
    data: jQuery('#' + form_id).serialize(),
    dataType: 'json',
    success: function(line_item) {
      if ((typeof callback) === 'function') {
        callback(line_item);
      } else {
        StoreAPI.onItemAdded(line_item);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.getCart = function(callback) {
  jQuery.getJSON('/cart', function(cart, textStatus) {
    if ((typeof callback) === 'function') {
      callback(cart);
    } else {
      StoreAPI.onCartUpdate(cart);
    }
  });
};
StoreAPI.getCartShippingRatesForDestination = function(shipping_address, callback) {
  var params = {
    type: 'GET',
    url: '/cart/shipping_rates',
    data: StoreAPI.param({'shipping_address': shipping_address}),
    dataType: 'json',
    success: function(response) {
      rates = response.shipping_rates
      if ((typeof callback) === 'function') {
        callback(rates, shipping_address);
      } else {
        StoreAPI.onCartShippingRatesUpdate(rates, shipping_address);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  }
  jQuery.ajax(params);
};
StoreAPI.getProduct = function(handle, callback) {
  jQuery.getJSON('/products/' + handle + '', function(product, textStatus) {
    if ((typeof callback) === 'function') {
      callback(product);
    } else {
      StoreAPI.onProduct(product);
    }
  });
};
StoreAPI.changeItem = function(variant_id, quantity, callback) {
  var params = {
    type: 'POST',
    url: '/cart/change',
    data: 'quantity=' + quantity + '&id=' + variant_id,
    dataType: 'json',
    success: function(cart) {
      if ((typeof callback) === 'function') {
        callback(cart);
      } else {
        StoreAPI.onCartUpdate(cart);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.removeItem = function(variant_id, callback) {
  var params = {
    type: 'POST',
    url: '/cart/change',
    data: 'quantity=0&id=' + variant_id,
    dataType: 'json',
    success: function(cart) {
      if ((typeof callback) === 'function') {
        callback(cart);
      } else {
        StoreAPI.onCartUpdate(cart);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.clear = function(callback) {
  var params = {
    type: 'POST',
    url: '/cart/clear',
    data: '',
    dataType: 'json',
    success: function(cart) {
      if ((typeof callback) === 'function') {
        callback(cart);
      } else {
        StoreAPI.onCartUpdate(cart);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.updateCartFromForm = function(form_id, callback) {
  var params = {
    type: 'POST',
    url: '/cart/update',
    data: jQuery('#' + form_id).serialize(),
    dataType: 'json',
    success: function(cart) {
      if ((typeof callback) === 'function') {
        callback(cart);
      } else {
        StoreAPI.onCartUpdate(cart);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.updateCartAttributes = function(attributes, callback) {
  var data = '';
  if (jQuery.isArray(attributes)) {
    jQuery.each(attributes, function(indexInArray, valueOfElement) {
      var key = attributeToString(valueOfElement.key);
      if (key !== '') {
        data += 'attributes[' + key + ']=' + attributeToString(valueOfElement.value) + '&';
      }
    });
  } else if ((typeof attributes === 'object') && attributes !== null) {
    jQuery.each(attributes, function(key, value) {
      data += 'attributes[' + attributeToString(key) + ']=' + attributeToString(value) + '&';
    });
  }
  var params = {
    type: 'POST',
    url: '/cart/update',
    data: data,
    dataType: 'json',
    success: function(cart) {
      if ((typeof callback) === 'function') {
        callback(cart);
      } else {
        StoreAPI.onCartUpdate(cart);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
StoreAPI.updateCartNote = function(note, callback) {
  var params = {
    type: 'POST',
    url: '/cart/update',
    data: 'note=' + attributeToString(note),
    dataType: 'json',
    success: function(cart) {
      if ((typeof callback) === 'function') {
        callback(cart);
      } else {
        StoreAPI.onCartUpdate(cart);
      }
    },
    error: function(XMLHttpRequest, textStatus) {
      StoreAPI.onError(XMLHttpRequest, textStatus);
    }
  };
  jQuery.ajax(params);
};
if (jQuery.fn.jquery >= '1.4') {
  StoreAPI.param = jQuery.param;
} else {
  StoreAPI.param = function(a) {
    var s = [],
      add = function(key, value) {
        value = jQuery.isFunction(value)
          ? value()
          : value;
        s[s.length] = encodeURIComponent(key) + "=" + encodeURIComponent(value);
      };
    if (jQuery.isArray(a) || a.jquery) {
      jQuery.each(a, function() {
        add(this.name, this.value);
      });
    } else {
      for (var prefix in a) {
        StoreAPI.buildParams(prefix, a[prefix], add);
      }
    }
    return s.join("&").replace(/%20/g, "+");
  }
  StoreAPI.buildParams = function(prefix, obj, add) {
    if (jQuery.isArray(obj) && obj.length) {
      jQuery.each(obj, function(i, v) {
        if (rbracket.test(prefix)) {
          add(prefix, v);
        } else {
          StoreAPI.buildParams(prefix + "[" + (typeof v === "object" || jQuery.isArray(v)
            ? i
            : "") + "]", v, add);
        }
      });
    } else if (obj != null && typeof obj === "object") {
      if (StoreAPI.isEmptyObject(obj)) {
        add(prefix, "");
      } else {
        jQuery.each(obj, function(k, v) {
          StoreAPI.buildParams(prefix + "[" + k + "]", v, add);
        });
      }
    } else {
      add(prefix, obj);
    }
  }
  StoreAPI.isEmptyObject = function(obj) {
    for (var name in obj) {
      return false;
    }
    return true;
  }
  StoreAPI.ExpressionSpecialChars = [
    {
      key: '(',
      val: '%26'
    }, {
      key: ')',
      val: '%27'
    }, {
      key: '|',
      val: '%28'
    }, {
      key: '-',
      val: '%29'
    }, {
      key: '&',
      val: '%30'
    }
  ];
  StoreAPI.encodeExpressionValue = function(val) {
    if ((typeof val) !== 'string' || val == null || val == "")
      return val;
    val = val.replace('%', '%25');
    for (n = 0; n < StoreAPI.ExpressionSpecialChars.length; n++) {
      var char = StoreAPI.ExpressionSpecialChars[n];
      val = val.replace(char.key, char.val);
    }
    return val;
  }
}
function floatToString(numeric, decimals) {
  var amount = numeric.toFixed(decimals).toString();
  amount.replace('.', StoreAPI.decimal);
  if (amount.match('^[\.' + StoreAPI.decimal + ']\d+')) {
    return "0" + amount;
  } else {
    return amount;
  }
}
function attributeToString(attribute) {
  if ((typeof attribute) !== 'string') {
    attribute += '';
    if (attribute === 'undefined') {
      attribute = '';
    }
  }
  return jQuery.trim(attribute);
}
