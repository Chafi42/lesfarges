(function ($) {
  var ColorField = function (color_field) {
    var self = this;

    self.colorField = color_field;
    self.colorGrp = $(self.colorField).find(".group-color");
    self.dataPickr = $(self.colorField).data("picker");
    self.colorBtn = $(self.colorField).find(".add-color");
    self.isInput = $(self.colorField).attr("data-input");

    if (self.colorGrp.length) {
      $(self.colorGrp).each(function (index, group) {
        let groupInput = $(group).find("input.color-input");
        // If color picker have an input text field
        let color;
        if (self.isInput) {
          // Use regex to get value from attribute name
          color = self.getColorHex(groupInput);
        } else {
          // Just get the value
          color = $(groupInput).val();
        }
        self.setPicker(group, color);
      });
    }

    $(self.colorField).on(
      "click",
      ".add-color",
      { colorField: self },
      self.onAddColor
    );

    $(self.colorField).on(
      "click",
      ".del-color",
      { colorField: self },
      self.onDelColor
    );
  };

  ColorField.prototype.onAddColor = function (event) {
    let self = event.data.colorField;
    let colorGroup = self.createColorGroup();

    $(colorGroup).insertBefore(this);

    let pickr = self.setPicker(colorGroup);
    pickr.show();

    let groupLength = $(self.colorField).find(".group-color").length;

    if (!self.dataPickr.multi && groupLength >= 1) {
      $(self.colorBtn).remove();
    }
  };

  ColorField.prototype.onDelColor = function (event) {
    let self = event.data.colorField;
    $(this).parent().remove();
    let groupLength = $(self.colorField).find(".group-color").length;
    if (!self.dataPickr.multi && groupLength === 0) {
      self.addColorButton();
    }
  };

  ColorField.prototype.createColorGroup = function () {
    let colGroup = document.createElement("div");
    colGroup.className = this.dataPickr.groupClass + " group-color";

    let colorPicker = document.createElement("div");
    colorPicker.className = "color-pickr d-inline-block";

    let input = document.createElement("input");
    input.type = "text";
    input.className = this.dataPickr.inputClass;
    input.setAttribute("name", this.dataPickr.inputName);
    input.setAttribute("value", this.dataPickr.inputValue);

    let delSpan = document.createElement("span");
    delSpan.className =
      "del-color button bg-transparent border-0 text-danger pl-1";
    let delIcon = document.createElement("i");
    delIcon.className = "fas fa-times";
    delSpan.appendChild(delIcon);

    colGroup.appendChild(colorPicker);
    colGroup.appendChild(input);
    colGroup.appendChild(delSpan);

    return colGroup;
  };

  ColorField.prototype.getColorHex = function (input) {
    return $(input)
      .attr("name")
      .match(/\[(.*?)\]/g)
      .pop()
      .replace(/\[|\]/g, "");
  };

  ColorField.prototype.updateColorName = function (input, colorHex) {
    let self = this;
    // Is color with input text
    if (self.isInput) {
      let color = self.getColorHex(input);
      let inputName = $(input).attr("name").replace(color, colorHex);
      $(input).attr("name", inputName);
    } else {
      $(input).attr("value", colorHex);
    }
  };

  ColorField.prototype.setPicker = function (colorGroup, defaultColor) {
    let self = this;
    let colorPickr = $(colorGroup).find(".color-pickr")[0];
    var inputSave = $(colorGroup).find("input.color-input");
    defaultColor =
      typeof defaultColor !== "undefined"
        ? defaultColor
        : self.dataPickr.defaultColor;
    const pickr = new Pickr({
      el: colorPickr,
      theme: "nano", // or 'monolith', or 'nano'
      appClass: "custom-class",
      default: defaultColor,
      swatches: self.dataPickr.swatches,

      defaultRepresentation: "HEXA",
      components: {
        preview: true,
        opacity: true,
        hue: true,

        interaction: {
          hex: false,
          rgba: false,
          hsva: false,
          input: true,
          clear: false,
          save: true,
        },
      },
    });

    pickr.on("save", function (color, instance) {
      self.updateColorName(inputSave, color.toHEXA().toString());
      pickr.hide();
    });

    return pickr;
  };

  ColorField.prototype.addColorButton = function () {
    let addColor = document.createElement("div");
    addColor.className = this.dataPickr.groupClass + " add-color";

    let spanButton = document.createElement("span");
    spanButton.className =
      "button rounded-circle text-dark border-dark bg-transparent";

    let icon = document.createElement("i");
    icon.className = "fas fa-plus";

    spanButton.appendChild(icon);
    addColor.appendChild(spanButton);

    $(this.colorField).append(addColor);
    this.colorBtn = $(addColor);
  };

  /**
   * Function to call color_picker on jquery selector.
   */
  $.fn.color_picker = function () {
    new ColorField(this);
    return this;
  };

  $(document).ready(function () {
    if ($(".color-field").length) {
      $(".color-field").each(function () {
        $(this).color_picker();
      });
    }
  });
})(jQuery);
