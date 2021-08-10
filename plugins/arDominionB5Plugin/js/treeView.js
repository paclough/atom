(function ($) {
  "use strict";

  $(() => new Treeview($("#treeview")));

  /**
   * Debounces a function. Returns a function that calls the original fn function only if no invocations have been made
   * within the last quietMillis milliseconds.
   *
   * @param quietMillis number of milliseconds to wait before invoking fn
   * @param fn function to be debounced
   * @return debounced version of fn
   */
  function debounce(quietMillis, fn) {
    var timeout;
    return function () {
      window.clearTimeout(timeout);
      timeout = window.setTimeout(fn, quietMillis);
    };
  }

  function killEvent(event) {
    event.preventDefault();
    event.stopPropagation();
  }

  class Treeview {
    constructor($element) {
      this.$element = $element;

      if (!this.$element.length) {
        return;
      }

      // Used to control loading status and block interface if needed
      this.setLoading(false);

      // Regular nodes selector
      this.nodesSelector = "li:not(.ancestor, .more)";

      // Store the current resource id to highlight it
      // during the treeview browsing
      this.resourceId = this.$element.data("current-id");

      // Check if the treeview is sortable
      this.sortable =
        undefined !== this.$element.data("sortable") &&
        !!this.$element.data("sortable");

      // Check if the treeview is used in the browser page
      this.browser =
        undefined !== this.$element.data("browser") &&
        !!this.$element.data("browser");

      // Menu (tabs) and search box
      this.$menu = this.$element.parent().prev("#treeview-menu");
      this.$search = this.$element.siblings("#treeview-search");
      this.$list = this.$element.siblings("#treeview-list");

      this.init();
    }
    init() {
      this.$element
        .on("click.treeview.atom", "li", this.click.bind(this))
        .on("mousedown.treeview.atom", "li", this.mousedownup.bind(this))
        .on("mouseup.treeview.atom", "li", this.mousedownup.bind(this))
        .on(
          "mouseenter.treeview.atom",
          ".list-group-item",
          this.listItemMouseEnter.bind(this)
        )
        .on(
          "mouseleave.treeview.atom",
          ".list-group-item",
          this.listItemMouseLeave.bind(this)
        )
        .bind("scroll", this.scroll.bind(this))
        .bind("scroll-debounced", this.debouncedScroll.bind(this))
        .bind("mousewheel", this.mousewheel.bind(this));

      this.$search
        .on("submit.treeview.atom", "form", this.search.bind(this))
        .on("keydown.treeview.atom", "input", this.searchChange.bind(this))
        .on(
          "mouseenter.treeview.atom",
          ".list-group-item",
          this.listItemMouseEnter.bind(this)
        )
        .on(
          "mouseleave.treeview.atom",
          ".list-group-item",
          this.listItemMouseLeave.bind(this)
        );

      this.$list.on(
        "click.treeview.atom",
        ".pager a",
        this.clickPagerButton.bind(this)
      );

      // Search box auto focus.
      this.$menu.on("shown.bs.tab", "#treeview-search-tab", (event) => {
        this.$search.find("input").focus();
      });

      var self = this;
      this.notify = debounce(80, function (e) {
        self.$element.trigger("scroll-debounced", e);
      });

      this.installSortableBehavior();
    }
    setLoading(status, $node) {
      this.loading = status;

      if (this.loading) {
        this.$element.addClass("loading");

        if ($node) {
          // Add loading icon
          $node.append('<div class="loading" />');
          $node.children("i").css("visibility", "hidden");
        }
      } else {
        this.$element.removeClass("loading");

        if ($node) {
          // Remove loading icon
          $node
            .children(".loading")
            .remove()
            .end()
            .children("i")
            .css("visibility", "visible");
        }
      }

      return this;
    }
    installSortableBehavior() {
      // Create jquery-ui sortable object
      if (!this.sortable) {
        return this;
      }

      this.$element.sortable({
        items: this.nodesSelector,
        placeholder: "placeholder",
        forcePlaceholderSize: true,
        start: this.drag.bind(this),
        stop: this.drop.bind(this),
        axis: "y",
      });

      this.$element.disableSelection();

      this.showGrip();
    }
    refreshSortableBehavior() {
      // Create jquery-ui sortable object
      if (!this.sortable) {
        return this;
      }

      var nodes = this.$element.find(this.nodesSelector);

      if (1 < nodes) {
        nodes.sortable("refresh");
      }

      this.showGrip();

      return this;
    }
    showGrip() {
      this.$element
        .find(".grip")
        .remove()
        .end()
        .find(this.nodesSelector)
        .prepend('<small class="grip"></small>');

      return this;
    }
    mousedownup(e) {
      if (this.loading) {
        killEvent(e);
      }
    }
    drag(e, ui) {
      this._position = ui.item.prev().index();

      // Left position needs to be reset.
      const left = ui.item.parents("ul").offset().left;
      ui.item.css("left", left);

      const popover = bootstrap.Popover.getInstance(ui.item.get(0));
      if (!popover) {
        return;
      }

      popover.hide();
    }
    drop(e, ui) {
      if (this._position == ui.item.prev().index()) {
        return this;
      }

      var $prev = ui.item.prev();
      var $next = ui.item.next();

      var data = {};

      if ($prev.is(".ancestor")) {
        data = { move: "moveBefore", target: $next.data("xhr-location") };
      } else {
        data = { move: "moveAfter", target: $prev.data("xhr-location") };
      }

      $.ajax({
        url: ui.item.data("xhr-location").replace(/treeView$/, "treeViewSort"),
        context: this,
        dataType: "html",
        data: data,
        beforeSend: function () {
          this.setLoading(true, ui.item);
        },
        success: function () {
          // Green highlight effect
          ui.item.effect("highlight", { color: "#dff0d8" }, 500);
        },
        complete: function () {
          this.setLoading(false, ui.item);
        },
        error: function (jqXHR, textStatus, thrownError) {
          // Cancel event if HTTP error
          // Item will be moved back to its original position
          if (thrownError.length) {
            this.$element.sortable("cancel");
          }

          // Red highlight effect
          ui.item.effect("highlight", { color: "#f2dede" }, 500);
        },
      });

      return this;
    }
    // Prevent out-of-bounds scrollings via mousewheel.
    mousewheel(e, delta, deltaX, deltaY) {
      var top = this.$element.scrollTop();
      if (deltaY > 0 && top - deltaY <= 0) {
        this.$element.scrollTop(0);
        killEvent(e);
      } else if (
        deltaY < 0 &&
        this.$element.get(0).scrollHeight -
          this.$element.scrollTop() +
          deltaY <=
          this.$element.height()
      ) {
        this.$element.scrollTop(
          this.$element.get(0).scrollHeight - this.$element.height()
        );
        killEvent(e);
      }
    }
    scroll(e) {
      if (e.target.contains(this.$element.get(0))) {
        this.notify(e);
      }
    }
    debouncedScroll(e) {
      var $target = $(e.target);

      e.preventDefault();

      // Detect when users scrolls to the bottom
      if (
        $target.scrollTop() + $target.innerHeight() >=
        $target.get(0).scrollHeight
      ) {
        var self = this;

        // Delay the trigger
        window.setTimeout(function () {
          var $more = self.$element.find(".more:last");

          // Make sure that we have selected the nextSiblings button
          if (0 < $more.next().length) {
            return;
          }

          $more.trigger("click");
        }, 250);
      }
    }
    click(e) {
      var $li =
        "LI" === e.target.tagName ? $(e.target) : $(e.target).closest("li");

      if (this.loading && "A" !== e.target.tagName) {
        killEvent(e);

        return;
      }

      // When the [...] button is clicked
      if ($li.hasClass("more")) {
        killEvent(e);

        return this.showMore($li);
      }

      // When the arrow is clicked
      else if ("I" === e.target.tagName) {
        if ($li.hasClass("root")) {
          killEvent(e);

          return this;
        }

        return this.showItem($li);
      }

      return this;
    }
    showItem($element) {
      this.setLoading(true, $element);

      // Figure out if the user is try to collapse looking at the ancestor class
      var collapse = $element.hasClass("ancestor");

      // Check if the element has a previous ancestor
      var hasAncestor = $element.prev().hasClass("ancestor");

      // When collapsing a top-level item show prev and next siblings
      if (collapse && !hasAncestor) {
        var show = "itemAndSiblings";
        var url = $element.data("xhr-location");
      } else {
        var show = "item";
        var url = collapse
          ? $element.prev().data("xhr-location")
          : $element.data("xhr-location");
      }

      $.ajax({
        url: url,
        context: this,
        dataType: "html",
        data: {
          show: show,
          resourceId: this.resourceId,
          browser: this.browser,
        },
      })

        .fail(function (fail) {
          // Hide the expand icon if not found
          if (404 == fail.status) {
            $element.removeClass("expand").children("i").remove();
          }
        })

        .done(function (data) {
          if (collapse && !hasAncestor) {
            $element.nextAll().remove();
            $element.replaceWith(data);
          } else if (collapse) {
            $element.nextAll().addBack().remove();

            this.$element.find(".ancestor:last-child").after(data);
          } else {
            var nodes = this.$element.find(this.nodesSelector);
            var lastAncestor = nodes.eq(0).prev();

            // Check if is really an ancestor
            if (lastAncestor.hasClass("ancestor")) {
              nodes.remove();
              this.$element.find(".more").remove();
              lastAncestor
                .after($element)
                .next()
                .addClass("ancestor")
                .removeClass("expand")
                .after(data);
            } else {
              this.$element.find(".more").remove();
              $element.addClass("ancestor").removeClass("expand");
              var removeNodes = this.$element.find(this.nodesSelector);
              removeNodes.remove();
              $element.after(data);
            }
          }

          this.refreshSortableBehavior();
        })

        .always(function (data) {
          this.setLoading(false, $element);
        });

      return this;
    }
    showMore($element) {
      var $a = $element.find("a");
      var loadingId = window.setInterval(function () {
        $a.append(".");
      }, 125);

      var showAction = $element.next().is("LI")
        ? "prevSiblings"
        : "nextSiblings";

      $.ajax({
        url: $element.data("xhr-location"),
        context: this,
        dataType: "html",
        data: {
          show: showAction,
          resourceId: this.resourceId,
          browser: this.browser,
        },
        beforeSend: function () {
          this.setLoading(true, $element);
        },
        success: function (data) {
          $element.replaceWith(data);

          this.refreshSortableBehavior();
        },
        complete: function () {
          this.setLoading(false, $element);

          window.clearTimeout(loadingId);
        },
        error: function () {},
      });
    }
    clearSearchResults() {
      this.$search.find(".list-group, .no-results").remove();
    }
    showSearchAlert(message, bsAlertClass = "alert-warning") {
      const $alert = $(
        '<div class="no-results alert rounded-0 rounded-bottom border-top-0" role="alert"></div>'
      )
        .html(message)
        .addClass(bsAlertClass);

      this.$search.append($alert);
    }
    search(event) {
      event.preventDefault();

      var query = event.target.query.value;
      if (1 > query.length || this.loading) {
        return this;
      }

      // Obtain queryField value
      var queryField = this.$search.find(
        'input[type="radio"][name="queryField"]:checked'
      );
      if (queryField.length > 0) {
        var queryFieldValue = queryField.val();
        var data = { subquery: query, subqueryField: queryFieldValue };
      } else {
        var data = { query: query };
      }

      this.setLoading(true);

      $.ajax({
        url: event.target.action,
        context: this,
        dataType: "json",
        data: data,
      })

        .always(function (data) {
          this.clearSearchResults();
        })

        .fail(function (fail) {
          if (404 == fail.status) {
            this.showSearchAlert(event.target.getAttribute("data-not-found"));
          } else {
            this.showSearchAlert(
              event.target.getAttribute("data-error"),
              "alert-warning"
            );
          }
        })

        .done(function (data) {
          // Add new list
          this.$search.append(
            '<ul class="list-group list-group-flush rounded-0 border border-top-0"></ul>'
          );

          const $listItemTmpl = $(
            '<a href="#" class="list-group-item list-group-item-action"></a>'
          );

          // Inject results
          var $list = this.$search.find(".list-group");
          for (var i in data.results) {
            const item = data.results[i];
            const $listItem = $listItemTmpl
              .clone()
              .attr("href", item.url)
              .attr("data-title", item.level)
              .attr("data-content", item.identifier + item.title)
              .html(item.title);
            $list.append($listItem);
          }

          // Inject the browse link, which is part of the server payload.
          if (undefined !== data.more) {
            const $serverLink = $(data.more).children("a");

            // New link from scratch.
            const href = $serverLink.attr("href");
            const text = $serverLink.text().trim();
            $list.append(
              $(
                '<a class="btn btn-sm atom-btn-white w-100 border-0 rounded-0">' +
                  '<i class="fas fa-search me-1" aria-hidden="true"></i>' +
                  "</a>"
              )
                .attr("href", href)
                .append(text)
            );
          }
        })

        .always(function (data) {
          var self = this;
          window.setTimeout(function () {
            self.setLoading(false);
          }, 250);
        });

      return this;
    }
    // Clear search input when the escape key is pressed.
    searchChange(event) {
      switch (event.which) {
        case 27:
          this.clearSearchResults();
          event.target.value = "";
      }
    }
    // Create and show the popover.
    listItemMouseEnter(event) {
      const target = event.target;
      const listItem =
        target.tagName in ["LI", "A"]
          ? target
          : target.closest(".list-group-item");

      // This is happening but I can't tell why.
      if (!listItem.dataset.content) {
        return;
      }

      const popover = bootstrap.Popover.getOrCreateInstance(listItem, {
        html: true,
        placement: "auto",
        content: listItem.dataset.content,
        title: listItem.dataset.title ?? "", // Not always defined.
      });

      popover.show();
    }
    // hide the popover.
    listItemMouseLeave(event) {
      const target = event.target;
      const listItem =
        target.tagName in ["LI", "A"]
          ? target
          : target.closest(".list-group-item");

      const popover = bootstrap.Popover.getInstance(listItem);
      if (!popover) {
        return;
      }

      popover.hide();
    }
    clickPagerButton(event) {
      event.preventDefault();

      this.setLoading(true);

      $.ajax({
        url: event.target.href,
        context: this,
        dataType: "json",
      })

        .fail(function (fail) {
          if (404 == fail.status) {
            this.$list.find("ul, section").remove();
          }
        })

        .done(function (data) {
          this.$list.find("ul, section").remove();
          this.$list.append("<ul></ul>");

          var $list = this.$list.find("ul");
          for (var i in data.results) {
            var item = data.results[i];
            var link = '<a href="' + item.url + '">' + item.title + "</a>";
            $list.append("<li></li>").children(":last-child").append(link);
          }

          // Show more
          if (undefined !== data.more) {
            $list.after(data.more);
          }
        })

        .always(function (data) {
          var self = this;
          window.setTimeout(function () {
            self.setLoading(false);
          }, 250);
        });

      return this;
    }
  }
})(window.jQuery);
