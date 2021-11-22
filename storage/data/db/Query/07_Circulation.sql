SELECT
    "Id",
    "BookId", "MemberId",
    "BorrowedAt", "ReturnedAt"
FROM public."Circulation"
ORDER BY "Id" ASC
LIMIT 100;