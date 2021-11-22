SELECT
    "Id", "Code", "Name",
    "Level", "ParentId",
    "CreatedAt", "UpdatedAt"
FROM public."BookCategories"
ORDER BY "Id" ASC
LIMIT 100;