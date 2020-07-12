var eventFormatter = {
  capitalize: function(str) {
    if (!str) { return str; }
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  },
  formatTitle: function(eventObj) {
    if (!eventObj) {
      console.error("Missing eventObj");
    }
    let name = eventObj.game;
    if (eventObj.title) {
      name += (name ? ': ' : '') + eventObj.title
    }
    return name;
  },
  formatGmObj: function(obj) {
    if (!obj) { return '' };
    let name = obj.lastName;
    if (obj.firstName) {
      name = obj.firstName + ' ' + name;
    }
    if (obj.groupName) {
      name += ' (' + obj.groupName + ')';
    }
    return name;
  },
  formatPlayers: function(obj) {
    if (!obj) { return '' };
    let players = obj['maxplayers'];
    let minplayers = obj['minplayers'];
    if (minplayers && minplayers>0 && minplayers != players) {
      players = minplayers + " - " + players;
    }
    return players;
  },
  formatSingleTime: function (time) {
    if (!time) return "";
    if (time < 12) return time+"a";
    else if (time == 12) return "12p";
    else if (time < 24) return (time-12)+"p";
    else if (time == 24) return "12a";
    else return (time-24)+"a";
  },
  formatTime: function(eventObj) {
    let day = eventObj.day ? this.capitalize(eventObj.day) : '';
    let time = eventObj.time ? eventObj.time : '';
    let endtime = eventObj.endtime ? eventObj.endtime : '';
    return day + (time? ' ' + this.formatSingleTime(time) + '-'+this.formatSingleTime(endtime)+' ET':'');
  }
}
