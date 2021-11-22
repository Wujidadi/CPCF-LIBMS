SELECT
    "Id", "No", "Name", "OriginalName",
    "Author", "Illustrator", "Editor", "Translator",
    "Series",
    "Publisher", "PublishDate", "PublishDateType", "Edition", "Print",
    "StorageDate", "StorageType",
    "Deleted", "DeleteDate", "DeleteType",
    "Notes",
    "ISN", "EAN", "Barcode1", "Barcode2", "Barcode3",
    "CategoryId", "LocationId",
    "CreatedAt", "UpdatedAt"
FROM public."Books"
ORDER BY "Id" ASC
LIMIT 100;