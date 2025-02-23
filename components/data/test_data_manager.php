<?php
class Test_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************

  public const CATEGORIES =
<<<EOD
[
  [100, 'Liten'],
  [101, 'Medium'],
  [102, 'Stor']
]
EOD;

  public const PRODUCT_TYPES =
<<<EOD
[
  [100, '3 m&sup2;', 59, 100],
  [101, '3 m&sup2; &ndash; h&oslash;yt tak', 69, 100],
  [102, '7 m&sup2;', 89, 101],
  [103, '12 m&sup2;', 129, 102],
  [104, '20 m&sup2;', 159, 102]
]
EOD;

  public const LOCATIONS =
<<<EOD
[
  [100, 'Oslo vest', 'Slottsplassen 1', '0010', 'OSLO', 'Norge', '7-22 alle dager', 'Innend&oslash;rs, oppvarmet, lasterampe, heis'],
  [101, 'Oslo sentrum', 'Karl Johans gate 22', '0026', 'OSLO', 'Norge', '7-22 alle dager', 'Innend&oslash;rs, oppvarmet'],
  [102, 'Oslo nord', 'H&oslash;yesteretts plass 1', '0180', 'OSLO', 'Norge', '7-22 man-l&oslash;r', 'Innend&oslash;rs, oppvarmet'],
  [103, 'Bergen', 'Rådhusgaten 10', '5014', 'BERGEN', 'Norge', '10-24 man-fre', 'Innend&oslash;rs, oppvarmet'],
  [104, 'Spikkestad', 'Narums vei 8A', '3430', 'SPIKKESTAD', 'Norge', '10-20 (10-16)', 'Innend&oslash;rs, oppvarmet, gratis tilhenger'],
  [105, 'Trondheim', 'Kongsgårdsgata 2', '7013', 'TRONDHEIM', 'Norge', '0-24 alle dager', 'Innend&oslash;rs, oppvarmet']
]
EOD;

  public const LOCATIONS_WITH_ACCESS_CODE =
<<<EOD
[
  [100, 'Oslo vest', 'Slottsplassen 1', '0010', 'OSLO', 'Norge', '7-22 alle dager', 'Innend&oslash;rs, oppvarmet, lasterampe, heis', ''],
  [101, 'Oslo sentrum', 'Karl Johans gate 22', '0026', 'OSLO', 'Norge', '7-22 alle dager', 'Innend&oslash;rs, oppvarmet', ''],
  [102, 'Oslo nord', 'H&oslash;yesteretts plass 1', '0180', 'OSLO', 'Norge', '7-22 man-l&oslash;r', 'Innend&oslash;rs, oppvarmet', ''],
  [103, 'Bergen', 'Rådhusgaten 10', '5014', 'BERGEN', 'Norge', '10-24 man-fre', 'Innend&oslash;rs, oppvarmet', ''],
  [104, 'Spikkestad', 'Narums vei 8A', '3430', 'SPIKKESTAD', 'Norge', '10-20 (10-16)', 'Innend&oslash;rs, oppvarmet, gratis tilhenger', ''],
  [105, 'Trondheim', 'Kongsgårdsgata 2', '7013', 'TRONDHEIM', 'Norge', '0-24 alle dager', 'Innend&oslash;rs, oppvarmet', '6789']
]
EOD;

  public const PRODUCTS =
<<<EOD
[
  [1000, 'A 100', 100, 100, [], 0, '', ''],
  [1001, 'A 101', 100, 100, [], 1, '', ''],
  [1002, 'A 102', 100, 100, [], 2, '', '2024-01-01'],
  [1003, 'A 103', 100, 100, [], 3, '', '2024-01-01'],
  [1004, 'A 104', 100, 100, [], 4, '', ''],
  [1005, 'A 105', 100, 100, [], 4, '', ''],
  [1006, 'A 110', 100, 101, [], 4, '', ''],
  [1007, 'A 111', 100, 101, [], 4, '', ''],
  [1008, 'A 112', 100, 101, [], 5, '2023-12-31', ''],
  [1009, 'A 113', 100, 102, [], 6, '2023-11-30', '2024-01-01'],

  [1010, 'B 100', 101, 100, [], 0, '', ''],
  [1011, 'B 101', 101, 100, [], 1, '', ''],
  [1012, 'B 102', 101, 101, [], 2, '', '2024-01-01'],
  [1013, 'B 103', 101, 102, [], 3, '', '2024-01-01'],
  [1014, 'B 104', 101, 103, [], 4, '', ''],
  [1015, 'B 105', 101, 103, [], 4, '', ''],
  [1016, 'B 110', 101, 103, [], 4, '', ''],
  [1017, 'B 111', 101, 103, [], 4, '', ''],
  [1018, 'B 112', 101, 104, [], 5, '2023-12-31', ''],
  [1019, 'B 113', 101, 104, [], 6, '2023-11-30', '2024-01-01'],

  [1020, 'C 100', 102, 100, [], 0, '', ''],
  [1021, 'C 101', 102, 100, [], 1, '', ''],
  [1022, 'C 102', 102, 101, [], 2, '', '2024-01-01'],
  [1023, 'C 103', 102, 101, [], 3, '', '2024-01-01'],
  [1024, 'C 104', 102, 101, [], 4, '', ''],
  [1025, 'C 105', 102, 102, [], 4, '', ''],
  [1026, 'C 110', 102, 102, [], 4, '', ''],
  [1027, 'C 111', 102, 102, [], 4, '', ''],
  [1028, 'C 112', 102, 103, [], 5, '2023-11-30', ''],
  [1029, 'C 113', 102, 103, [], 6, '2023-11-30', '2024-01-01'],

  [1030, 'D 100', 103, 101, [], 0, '', ''],
  [1031, 'D 101', 103, 101, [], 1, '', ''],
  [1032, 'D 102', 103, 101, [], 2, '', '2024-01-01'],
  [1033, 'D 103', 103, 101, [], 3, '', '2024-01-01'],
  [1034, 'D 104', 103, 101, [], 5, '2023-12-31', ''],
  [1035, 'D 105', 103, 102, [], 5, '2023-11-30', ''],
  [1036, 'D 110', 103, 102, [], 4, '', ''],
  [1037, 'D 111', 103, 103, [], 4, '', ''],
  [1038, 'D 112', 103, 104, [], 5, '2023-11-30', ''],
  [1039, 'D 113', 103, 104, [], 6, '2023-11-30', '2024-01-01'],

  [1040, 'E 100', 104, 100, [], 0, '', ''],
  [1041, 'E 101', 104, 100, [], 1, '', ''],
  [1042, 'E 102', 104, 100, [], 2, '', '2024-01-01'],
  [1043, 'E 103', 104, 100, [], 3, '', '2024-01-01'],
  [1044, 'E 104', 104, 100, [], 4, '', ''],
  [1045, 'E 105', 104, 100, [], 4, '', ''],
  [1046, 'E 110', 104, 100, [], 4, '', ''],
  [1047, 'E 111', 104, 100, [], 4, '', ''],
  [1048, 'E 112', 104, 100, [], 5, '2023-11-30', ''],
  [1049, 'E 113', 104, 100, [], 6, '2023-11-30', '2024-01-01'],

  [1050, 'F 100', 105, 100, [], 0, '', ''],
  [1051, 'F 101', 105, 100, [], 1, '', ''],
  [1052, 'F 102', 105, 101, [], 2, '', '2024-01-01'],
  [1053, 'F 103', 105, 101, [], 3, '', '2024-01-01'],
  [1054, 'F 104', 105, 101, [], 4, '', ''],
  [1055, 'F 105', 105, 102, [], 4, '', ''],
  [1056, 'F 110', 105, 102, [], 5, '2023-12-31', ''],
  [1057, 'F 111', 105, 103, [], 5, '2023-11-30', ''],
  [1058, 'F 112', 105, 103, [], 6, '2023-11-30', '2024-02-01'],
  [1059, 'F 113', 105, 103, [], 6, '2023-11-30', '2023-12-01']
]
EOD;

  public const USERS =
<<<EOD
[
  [1000, 'Anders And', 'anders@mail.com', '98765432', true],
  [1001, 'Berit Berntsen', 'berit@mail.com', '97654321', false],
  [1002, 'Cecilie Carlsen', 'cecilie@mail.com', '49876543', false],
  [1003, 'David Didriksen', 'david@mail.com', '48765432', true],
  [1004, 'Endre Everything', 'endre@mail.com', '47654321', true]
]
EOD;

  public const USER =
<<<EOD
{
  id: 1000,
  name: 'Anders And',
  eMail: 'anders@mail.com',
  phone: '98765432'
}
EOD;

  public const SUBSCRIPTIONS =
<<<EOD
[
  [101, 'A 101', 103, '12 m&sup2;', 0, '2023-01-01', '2023-02-28', 'Normal', [[-1, ['2023-01-01', 129, 'Normal price']], [1, ['2023-01-01', 49, 'Normal price']]], null],
  [102, 'D 969', 105, '20 m&sup2;', 1, '2023-03-01', '', 'Pluss', [[-1, ['2023-03-01', 159, 'Normal price']], [1, ['2023-03-01', 79, 'Normal price']]], null]
];
EOD;

  public const AVAILABLE_PRODUCT_TYPES =
<<<EOD
[
  [100, "3 m&sup2;", 59, 100, true, [], null, [1000, 1001, 1004]],
  [101, "3 m&sup2; &ndash; h&oslash;yt tak", 69, 100, true, [], null, [1006, 1007]],
  [102, "7 m&sup2;", 89, 101, false, [100, 104], "2024-03-01", []],
  [103, "12 m&sup2;", 129, 102, false, [], "2023-04-01", []],
  [104, "20 m&sup2;", 159, 102, true, [], null, [1039]]
]
EOD;

  public const INSURANCE_PRODUCTS =
<<<EOD
[
  [1000, 'Normal', 'Opptil 10000 kr, egenandel 2000 kr', 49, null, null, null],
  [1001, 'Pluss', 'Opptil 100000 kr, egenandel 1000 kr', 79, [102, 103, 104], [100, 101, 102, 103, 105], null],
  [1002, 'Pluss (Spikkestad)', 'Opptil 100000 kr, egenandel 1000 kr', 79, [102, 103, 104], [104], null],
  [1003, 'Super', 'Opptil 1000000 kr, egenandel 1000 kr', 129, null, [100, 101, 102, 103, 104], null],
]
EOD;

  public const PAYMENT_HISTORY_ODD =
<<<EOD
[
  [100003, "M&aring;nedsfaktura", "100003", 3, "2023-07-01", "2023-07-15", "Betalt 2023-07-15", "status-green"],
  [100004, "M&aring;nedsfaktura", "100004", 3, "2023-08-01", "2023-08-15", "Betalt 2023-08-12", "status-green"],
  [100005, "M&aring;nedsfaktura", "100005", 3, "2023-09-01", "2023-09-15", "Betalt 2023-09-15", "status-green"],
  [100006, "M&aring;nedsfaktura", "100006", 3, "2023-10-01", "2023-10-15", "Ikke betalt", "status-red"],
  [100007, "Betalingsp&aring;minnelse", "100007", 3, "2023-10-17", "2023-11-02", "Ikke betalt", "status-red"],
  [100008, "Inkassovarsel", "100008", 3, "2023-11-05", "2023-11-20", "Betalt 2023-11-10", "status-green"],
  [100009, "M&aring;nedsfaktura", "100009", 3, "2023-11-01", "2023-11-15", "Betalt", "status-green"],
  [100010, "M&aring;nedsfaktura", "100010", 3, "2023-12-01", "2023-12-15", "Utsendt", "status-blue"]
]
EOD;

  public const PAYMENT_HISTORY_EVEN =
<<<EOD
[
  [100001, "M&aring;nedsfaktura", "100001", 1, "2023-10-01", "2023-10-01", "Trukket 2023-10-01", "status-green"],
  [100002, "M&aring;nedsfaktura", "100002", 0, "2023-11-01", "2023-11-01", "Trukket 2023-11-01", "status-green"],
  [100003, "M&aring;nedsfaktura", "100003", 0, "2023-12-01", "2023-12-01", "Trukket 2023-12-01", "status-green"]
]
EOD;

  public const CAPACITY_PRICE_RULES =
<<<EOD
[
  [102, "Standard prisvariasjon", "2024-01-01", "2024-12-31", [[-10, 0, 10], [-5, 10, 25], [5, 75, 90], [10, 90, 100]], null, null, false],
  [103, "Aggressiv prisvariasjon", "2024-01-01", "2024-12-31", [[-25, 0, 20], [-10, 20, 50], [10, 75, 90], [25, 90, 100]], [103, 104, 105], [103, 104], false]
]
EOD;

  public const SPECIAL_OFFER_PRICE_RULES =
<<<EOD
[
  [100, "Storrengjøring til påske 2024", "2024-03-22", "2024-04-01", [[-10, 0]], null, null, false],
  [101, "Sommerkampanje 2024", "2024-05-17", "2024-08-30", [[-100, 1], [-20, 3]], [103, 104, 105], [102, 103, 104], false]
]
EOD;

  // *******************************************************************************************************************
}
?>