<?php 

  $categories['BADGE_CATEGORY'] = "01";
  $categories['TICKET_CATEGORY'] = "02";
  $categories['SHIRT_CATEGORY'] = "03";
  $categories['MISC_CATEGORY'] = "04";

  // 2 digits Year
  // 2 digits Item Category
  // (T-Shirt, Badge, Ticket, etc)
  // 4 digits Item Code
  // (Large T-Shirt, Weekend badge, BGSA10-1 Ticket, etc)
  // 4 digits Item Sequence Number
  // (Sequential number, 0000-9999)

/**
 * 
 * @return string containing barcode for ticket
 */
function getTicketCode($year, $event)
{
  global $categories;

  $eventId = $event['id_event'];
  $itemCode = $eventId % 10000;

//  int quantity = resultSet.getInt("i_maxplayers");
//  double price = resultSet.getDouble("i_cost");

//  // TODO should we use the game name or event number?
//  String special = resultSet.getString("s_number");
//  String description = resultSet.getString("description");

  return substr($year, 2) 
    . $categories['TICKET_CATEGORY']
    . str_pad($itemCode, 4, "0");
}


//function createBadges($year)
//{
//    String sql = "select id_badge_type, s_type, i_price from ucon_badge_type";
//    try
//    {
//      Statement stmt = conn.createStatement();
//      ResultSet resultSet = stmt.executeQuery(sql);
//
//      PreparedStatement insertStmt = conn
//          .prepareStatement(INSERT_ITEM_SQL);
//
//      while (resultSet.next())
//      {
//        int itemCode = resultSet.getInt("id_badge_type");
//
//        String subtype = resultSet.getString("s_type");
//        double price = resultSet.getDouble("i_price");
//        String description = "Badge - " + subtype;
//        Integer quantity = null;
//
//        String barcode = Integer.toString(year).substring(2)
//            + BADGE_CATEGORY + pad(itemCode, -4, "0");
//
//        createItem(insertStmt, barcode, year, "Badge", subtype, "",
//            description, price, quantity);
//        exportBarcode("Badge" + pad(itemCode, -4, "0"), barcode);
//      }
//    } catch (SQLException e)
//    {
//      logger.error("Could not load badges", e);
//    }
//}

//function createShirts($year)
//{
//    String sql = "select s_size, i_price from ucon_shirt_type";
//    try
//    {
//      Statement stmt = conn.createStatement();
//      ResultSet resultSet = stmt.executeQuery(sql);
//
//      PreparedStatement insertStmt = conn
//          .prepareStatement(INSERT_ITEM_SQL);
//
//      int itemCode = 0;
//      while (resultSet.next())
//      {
//        ++itemCode;
//
//        String subtype = resultSet.getString("s_size");
//        double price = resultSet.getDouble("i_price");
//        String description = "Shirt - " + subtype;
//        Integer quantity = null;
//
//        String barcode = Integer.toString(year).substring(2)
//            + SHIRT_CATEGORY + pad(itemCode, -4, "0");
//
//        createItem(insertStmt, barcode, year, "Shirt", subtype, "",
//            description, price, quantity);
//        exportBarcode("Shirt-" + subtype, barcode);
//      }
//    } catch (SQLException e)
//    {
//      logger.error("Could not load badges", e);
//    }
//}
//  
//function createMiscItem($year,
//      int itemId, String imgName, String itemDesc, double price)
//  {
//    String barcode = Integer.toString(year).substring(2) + MISC_CATEGORY + pad(itemId, -4, "0");
//    createItem(conn, barcode, year, "Misc", Integer.toString(itemId), "", itemDesc, price, null);
//    exportBarcode(imgName, barcode);
//  }
//  
//  
//function createMisc($year)
//{
//    final int ID_GENERIC = 0;
//    final int ID_DICE = 1;
//    final int ID_PUFFING = 2;
//    final int ID_MAYFAIR = 3;
//    createMiscItem(conn, year, ID_GENERIC, "misc-generic", "Generic Ticket", 1.50);
//    createMiscItem(conn, year, ID_DICE, "misc-dice", "U-Con Dice", 1.);
//    createMiscItem(conn, year, ID_PUFFING, "misc-puffingbilly", "Puffing Billy Ribbon", 15.);
//    createMiscItem(conn, year, ID_MAYFAIR, "misc-mayfair", "Mayfair Ribbon", 15.);
//}
