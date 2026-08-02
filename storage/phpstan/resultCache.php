<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1785608920,
	'meta' => array (
  'cacheVersion' => 'v13-packageDependencies',
  'phpstanVersion' => '2.2.7',
  'fnsr' => false,
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80212,
  'projectConfig' => '{parameters: {level: 5, paths: [C:\\xampp\\htdocs\\NexusSchool\\app], tmpDir: C:\\xampp\\htdocs\\NexusSchool\\storage\\phpstan}}',
  'analysedPaths' => 
  array (
    0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application',
    1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain',
    2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure',
    3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation',
    4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
  ),
  'scannedFiles' => 
  array (
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\AiUsageReport.php' => 'd2dc2bd4549cb90b91a7106af82d96d01a927364ec1e047bea24f17af0e8ab8e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\CalculateScheduledKpis.php' => '958e4af9081c3212d6af279ad14ae0aba09ebfbc7cb0c9f94764d619da5bd256',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\CreateDailySnapshots.php' => 'fe7842edfff26ee6d45135821e4dd6b3783c568ea0dd0117a7a9dea2877bc0e6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\PruneExpiredConversations.php' => '21397902fa55a9040f45bdc82f7ce3a074466df21c5933ccf5b028600d169ba4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\ScheduleReports.php' => '6b5ca176eabaa355b43db87a23f67170864073e33bd02fc0cb09115c434c2891',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\SystemBackupCommand.php' => 'ffb3975d2162fc37cac411255f06b630e3e8d4353fc49238bde8d0d89cb61477',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\SystemCleanupCommand.php' => 'd74ce0e7e47e9eec971e6ce7f8c10f6e4df2c88873488536aa37909248849e96',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\SystemHealthCheckCommand.php' => 'fdbd719c1a782eac0798009335283ef13e8e1c5dec914a8e794fa2b391a23cdc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\SystemMaintenanceCommand.php' => '1fee6b903288f49f1b4c42737efcd3f2d7b75e6f725ddfcc787936259efb504b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\System\\SystemBackupCommand.php' => 'f2862e146897af44ca98c3c8ea71f9be247d1d979a1aa35c69e3e5712b69ba78',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\System\\SystemCleanupCommand.php' => '1de020ee655ff27bffbf332aecaafff49ad818b604f209dac69a5daa8f4ccf75',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\System\\SystemHealthCheckCommand.php' => '3f838b698630e825136036ce84f74e74e4189fbf3ca1a883eea248abd23e5e5a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\System\\SystemMaintenanceCommand.php' => '1e0c8e905ef089a36adb65fe671a94d78400fbd4a2c40da69a2b8e22207413d8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\Workflow\\WorkflowCleanupStaleExecutions.php' => 'dbab4d4b1c9afb1931a94790765f8eb86096a4b78cca69cf28b673de2ae33de3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Console\\Commands\\Workflow\\WorkflowProcessSchedules.php' => '133a3f713fe275f079667fc55ba315bed187a992b6f9c14d62fdc176587fe51e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\DTOs\\AcademicClassData.php' => 'cf521e7f3610cf7e8612dc7d27d9774c61b3e20118ad7f13be1fbe510c5effdc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Enums\\AcademicClassStatus.php' => '519ee1ba9c6b92c0a24c0d6ac852bf335ba1ca0fccda252fa86dbbe46ca02274',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Events\\AcademicClassActivated.php' => 'ebe317880e3d91577a817796348d51c9ad18afe39e8c6a7e5a2e7e62ec51eada',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Events\\AcademicClassArchived.php' => 'c0fb85966ed5d1c4b7c7cfbc064872098ff15cc40fdbf0db8ee9b61706014353',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Events\\AcademicClassCreated.php' => '0d0278edd87232279fbedeb61eda7065ad2bc9c29e7a6aa6750c0c46875c19e4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Events\\AcademicClassUpdated.php' => 'a407f110ebb594b787bb59cd147b4ec7a09f4d8e9439a3fe2a717c412186de05',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Listeners\\LogAcademicClassActivated.php' => 'f8c933679d0f7d8e2dc0334c4b2201f0f8fe9deb082116eb83d3117ea3f73602',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Listeners\\LogAcademicClassArchived.php' => '80a1f2547ec7e7658a852adfb50a44a1b08ba8accb310504d2a957de9e5b1d53',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Listeners\\LogAcademicClassCreated.php' => '3c41930048c92080eda7627292b4a4efed66c092ac40eab622e2c7cb230162eb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Listeners\\LogAcademicClassUpdated.php' => '7e8cb6e61b72ab443d4a01bef7b712fdccb6137fc73a2a441f1f53dae0762764',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Policies\\AcademicClassPolicy.php' => '244285aa4bf3abdf98e8fe200c2d028a549366e91b4e69ef64267cee1ecccddb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Rules\\AcademicClassRules.php' => '0d31ad5768264c67bf18c06da2f531d1afb660e5dde2226718cf7f2ef7ecd268',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicClass\\Services\\AcademicClassService.php' => 'b237e9d116b7523b8078da366815c00bdfa9dd99019d28cc931c0d584bf5b8af',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\DTOs\\AcademicTermData.php' => '0effeca1ebfdd1fa2338d65e35f6a840e8d8b2ff39db8974eb0c2e4aff3a791a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Enums\\AcademicTermStatus.php' => 'a8b022bde4da0a5aadf048f4e78d3ade2d25f73908e310d101e8f746889f4d06',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Events\\AcademicTermActivated.php' => '64a7a7a9d6cdeb1ca31396cfdc1572667cb1ac3faff8a8effe6fe5092737b124',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Events\\AcademicTermArchived.php' => 'ba46f45dcea43abcb62c77808c2d04c1dfa1f4630b6dcb6ae0639426fe737be0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Events\\AcademicTermCreated.php' => 'c58a2a39ad63225ec58b8eda8129c83c12701ca06da332bee769dcf6895427e1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Events\\AcademicTermStatusChanged.php' => '8d786cb308fce6a1b9e383e3498e009356821fdcd7c11ea631637b3643e2e38d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Events\\AcademicTermUpdated.php' => '60a4742f8c5f9cffec1c33131cb8f904abdb7177fb37f715b261d0ca540ade56',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Listeners\\LogAcademicTermActivated.php' => '860179a83865c6422d5bbec364f277f91cbcfecf40576d584e0d754d64582689',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Listeners\\LogAcademicTermArchived.php' => 'a5da36daea13fb3604ca62141c77cf5fa7cd281910b41903117b32fcc755fc47',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Listeners\\LogAcademicTermCreated.php' => 'bb3cd29b3ce3cd477a41ccb417afb998f05657c8cb3faa05f70a8b1a0229cccb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Listeners\\LogAcademicTermStatusChanged.php' => '12970b7f64b372b1ba5d4c9c3703a486b59331f0dfc65dfc7568122aadb74366',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Listeners\\LogAcademicTermUpdated.php' => '887a464ad6a8de9f9004028db1dd533b9391c225d66ed89b23cf5f6e22888354',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Policies\\AcademicTermPolicy.php' => 'a394bb7081205fe532bdc5ce9bf4cc749e72c5f3a23cbad73d6b04893e81b18d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Rules\\AcademicTermRules.php' => 'a79941d8c1fbcea0bb72d5e507290a7591c2e6acc987960a3fdcc879b36b1e28',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicTerm\\Services\\AcademicTermService.php' => '781c554825f578c2a7d6e83a290d948963868908aec6cdf36a4c4108f9947ef4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\DTOs\\AcademicYearData.php' => 'ecf3a33bf7a46ec545a8ef8e065a764390a807c4a36ba734621a5a90fdc124ae',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Enums\\AcademicYearStatus.php' => 'ba3bc50122af2f30e621b001aa163cefa91fe01161090a393c94f4eddd97e1d3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearActivated.php' => '10b595c218eeae0b9eee8b11e57d5707ce44a4528be526bc72cb43b02b7a1570',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearArchived.php' => '9a2348fa6424f8b5be9911097419817dec58c38cb72bdcdd016425145d963562',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearCloned.php' => '72deda97db40c0f86d896c135720cf2b4f289c667427f5969ec8d7c90ed08099',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearCreated.php' => '46451bc7426f71cd7043287b6bcd7d7866f4712c04072a6e0132e0124d732c97',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearDatesChanged.php' => 'c5bf9aff7d392a9570f191116d547a6d2511bb7fcb422a9992ce86fa021c1872',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearDeleted.php' => 'e3a090e5e02ed733c42c899f6094de57aeb45a4e21dd1fdf957ffaa23cd668d4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearStatusChanged.php' => 'a6dd84fdeb32ca2a5126a8e6b5226936f3804272a27dc08c32115e2ffcb09f31',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearSwitched.php' => '3f91048a682b9c227b40d8e1b866baeda18c085ecc2f1d4876049eeed3225f9a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Events\\AcademicYearUpdated.php' => '8848f88060b5aa3660888e654c66c614d129ae74d01b9b54452f28f5bf0473b4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearActivated.php' => '2a5155bf301d33507f9dd761ec7aed029118f9f914f46398b0cb5668fba500bf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearArchived.php' => 'dad9f460cf62417d840dd762a4ce4a2d9839db95633c0810cfa63cb791cad704',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearCloned.php' => '4b15f9284e9db399b7f5ff76633c66af5051ace847e0adcbb732836e4306f2c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearCreated.php' => 'e06bb12e00df3e382bb6d3bcfd71e11122b0332f0244820a235b39c46a62e495',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearDatesChanged.php' => '4bc2e0b77880219c9b5fcc7fd08a79efe9c515ed8327e307b0adfad1ec792b09',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearDeleted.php' => '13e69c609ca9ff3ae898bb93885d203508b7332a82d95dc16493a00c611d981e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearStatusChanged.php' => '3e464f379cb80611cc95d0ce633805f9c1871890bab376ee26381cc54660ccab',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearSwitched.php' => '895bfd1589b88c5f73775af4cb5ffe40266ab8dab0c0ff8c0f3e6afe78042bc1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Listeners\\LogAcademicYearUpdated.php' => '411aa0d5023dfd7c50bd02a73a971d49879a68fb2d283a5875ff63b5a77143bc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Policies\\AcademicYearPolicy.php' => '0d48317f4422f43c5dc258028480467abfbae24efcb584a6650cf4afe630a411',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Rules\\AcademicYearRules.php' => '7656caa4c00422ea67b6753634cf031a7c47d06599546e448ef90a9c579d32b2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\AcademicYear\\Services\\AcademicYearService.php' => 'ab21f16464d912a2ec07562ba82afd607394ccc6e86a50808a5b57e2cc96c4ee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\AcademicServiceProvider.php' => '1c00ea54322a7b7c53bcae283dec69241d8771cd92ac8e57b0ec56f29cde91c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\ActivateCalendarYearAction.php' => '1b9f475f686f69f49f768d63cc5e2d277a2505ad63ba6f17639d797e3813162b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\ArchiveCalendarYearAction.php' => 'a681c6e2459f34acf106682eb79d9a6a317f56bbd64119f0c7ace87053863c63',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\CreateCalendarTermAction.php' => 'ca094caa47cf32186a9c8e2f8b37276348287c11e510bc14ef918770d430ffc2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\CreateCalendarYearAction.php' => '147ba69c449b3e46c6c622e641700fddee23fca9ec2726eb6a2822eb2cbcc9a1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\CreateClassSectionAction.php' => '2001d3a17958de9c2be2108f2dc3da96ecde88baeb7c2a3a42772295a3752fee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\CreateSchoolClassAction.php' => '60a530bbe2cd1cf9658e8e03330cc473ee0c82cba44ac77a98a5943b5d75dae1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\CreateStreamAction.php' => '6e26058664e151da2104f71f8bd4938a4f10d80bdf72bc0d33d66658086df1a2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Actions\\CreateSubjectAction.php' => '4941927452bcdd4e1abff6529b7be064605bf9ddd89a80bd09a527b47ffce28f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\CalendarEventData.php' => '4ab320664594ccfb75a496fb86a43b28bb3f0dd73a1f8c15edb4b0a87cc27dac',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\CalendarTermData.php' => '09026ea2a87da9b2f57357729f9c0f3f5549cd6ff470eb1d0bb48ba8e9e2e484',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\CalendarYearData.php' => 'b3663ba8b2b9468a520e1f70659171b52fc6a59dfe46e0853bcc5fc467816c10',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\ClassSectionData.php' => '019948597a06b9ad18547518cebcaebcee0ba4de1aa1f5242d8d63f86d39c004',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\CoefficientData.php' => '67b1b802cc737aed3c42e2912ea00890a80406a22bde759e63826e9d4b1eec2f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\EducationLevelData.php' => '475e9e0843b34c8946175df979571bfc7a9e9b247a1fe18d593422bdb4d92698',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\ProgramData.php' => '4b5f2e4ea548c582b867b60f19b147aea0f41a31cd0e517ab59dbdcdcfc3ac9d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\SchoolClassData.php' => '8904a5a212396307bcb65d852fb5784e0ccd77bbfd2cd76cff7b76669552b3e9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\StreamData.php' => '973d6fe71552303b5c8b67335a38286dd9323c07da03e11025f5533028418a53',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\SubjectData.php' => '3819a015decbca24ee3e06f3892f60c6472827f9b1d4776d11cb4ae5fa2a8862',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\DTOs\\SubjectGroupData.php' => '6b3718458d1730b985a504911e1634b4585cff881a8e4804800b5761ef3b0bf4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Services\\AcademicStructureService.php' => '919f0bbbfa13f62f6a2392e660eed41c2b80ff695ab6e843139ce50aa4f3fec1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Services\\CalendarService.php' => 'acd98f44537f6d406d644b3dabb36ae7feaa15f8c795ef7799b95b677a45a99e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Application\\Services\\SubjectCatalogService.php' => '512d5a29188f3ee58a88e8ac65850685eaafd27d3748f4a823c938e3d86c31d0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Contracts\\CalendarYearRepositoryInterface.php' => 'ca589c922dd6d8dd274c87a4bdf11c04fd2c9041fa5b5763f58bd52281291351',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Enums\\CalendarEventType.php' => '00a93cfa829276b3c3735f435166dea2050f2b7310e2a9716c3a818fdb81ba16',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Enums\\CalendarTermStatus.php' => 'e496e891dcb7b353a57b4ffd9453d6fae5288dfc0b2571967b8f2e55deec29f3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Enums\\CalendarYearStatus.php' => '073344efe26b6952a46d6c750fc125fc0061b0ff00ba7b2992d111cd5af27b20',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Enums\\CountryCode.php' => 'f4d3f9b3718a55cc55c321762e971896633c27a1f7465cd776acb195b0fd81fe',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Enums\\EntityStatus.php' => '42742e12998d7dc70d84d58196d337a787f6cb6ee691902f84f4ad48a41951e7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\CalendarTermCreated.php' => '98b4bd10c662a76fbea221f16315d267ef99c6348aebdbc06f3403c8ddeef417',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\CalendarYearActivated.php' => 'ec34f74381fc87ac293b21d1f1612d85f5825286ca926e9a3e0f8c625b0b83af',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\CalendarYearArchived.php' => '1371d4a76b20489036c4f7e8eaf27af35f338bce337cf822858ea8a01833f6ab',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\CalendarYearCreated.php' => '771795d490ed65b09fdbef8f5e98f38d4d8d7b2e3ac7ee919807d7e41b547f8a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\ClassSectionCreated.php' => '0aafd60bda143d6458ad1104b3bbc5c827146a027c95d6891c4e8b384ff6c74f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\SchoolClassCreated.php' => '1791e89523f035a288a1bc8fd090bc7eb9b5222367e9f66e9e2f09e80957db1f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\StreamCreated.php' => 'be64b689388ad2d0dfb103fca303bd87cbc1ca20e275c2f93c0f330d586e61e1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Events\\SubjectCreated.php' => '1fd4a09de9a2c1e1e85592dfc89b5c608574175d655896eff475ffbef90a64ba',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Exceptions\\ActiveCalendarYearAlreadyExistsException.php' => '2b8ce824bd78108cc915277a22a6520bb5d7caf6c5019aa1da4ef493636ed6b9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Exceptions\\DuplicateResourceException.php' => '4483af60b4f27791b052aef2617c3ca8c44e58e74347244019eed3c044a06e11',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Exceptions\\EntityInUseException.php' => '503bcbb9f75f02b80f6f604d6df28c96709fb1caf9db7eebcb8996bdc53fb416',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Exceptions\\OverlappingCalendarTermException.php' => 'f09825d5a16ee4d7ab4ad581901baad7cef5aabba7232c39a85ca17d4d1f5ff0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Exceptions\\TermOutsideCalendarYearException.php' => '0bfb20d1e1231cf8752a8a5a7397fd54355722e6b6f08700ec3d3700d7d40b82',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\Traits\\HandlesSchoolResourceAuthorization.php' => '6a5e5f1132b5295d755e5911fb7b80e5cf8252b18b5da0bbf48135adffb6dbe9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\ValueObjects\\Coefficient.php' => 'bb5d376e9cd691532032a7c39a4f96a610bbac38a36f2b4ef019ea9ae7e2415d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Domain\\ValueObjects\\DateRange.php' => '86a643303c8e0a688c233bd10db73c7349b5540bdb97ecc05cde069d0c5eeb7f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Listeners\\AcademicAuditLogger.php' => 'e0b778c047b2fc80ef225967de5670cdb5a92b3440bfcc4b813a836589015e44',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarEvent.php' => '082e6c15e29eebf2609d2e71dafcc97e64fe921433d52538fa7aca047489af3f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarTerm.php' => '6c75b63f5ebbf872397e50ca91ec2d29989823d3c71f76cd0175d7fc7284c684',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear.php' => 'be4592aca8036ea9cbb13e41224dab11bf3f35a14f15438fbcdd760808dc1c26',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\AcademicProgram.php' => '7b73899bf9b03e0d9da34ebc0e2caa9b2be879c7dad5f12ec41f9ac60f78c26a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\AcademicSubject.php' => 'ccf63507bb9dafaedf2e8c573b5a24c56710ee1bda8fdc5852a109f47712e3c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\ClassSection.php' => '78450f8701784b22cd6f865520e2577ac21a5e5818ac929256c8ed330a88a41e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\Coefficient.php' => 'e52d63d2464520229428242e06a347c90c7433216b70d4dd15e0924b298e6c6c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\EducationLevel.php' => '13d84a419298f09973c12b956cfd101dd2f1f6358dfe20b3b90b326c526d8ad1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass.php' => '49468322ca59fb438961b2fc2b7a098d68d106e1f01dc4f14156090c75c3edb3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\Stream.php' => 'b149f76ef5b70539b7e197fa75314577f1f8668f9640146a7ff4c049078e8d3c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Models\\SubjectGroup.php' => '8e4d041fa19967c804a6b182b6a5b774e107d0f39898de148a38e9a7ecf0ea29',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Observers\\AcademicEntityObserver.php' => 'd3a8d61c7c5428c237a7e0acfdff9a0da5282c65c4c444daa1bc0488eb5e5b6e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\AcademicCalendarEventPolicy.php' => 'c7f7f4a90f32e387ac162c4f5f842611a97374da85e8474ed06f8cdcfafd9b72',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\AcademicCalendarTermPolicy.php' => '56a390ce437b624097b8dda05d7ac7a90981f65880d96e38d53de5cf3443921f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\AcademicCalendarYearPolicy.php' => 'c40a5bfecf5f58c5e6f33a24a1c44de922db657eb8a3d68d3acd15b4beb18f63',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\AcademicProgramPolicy.php' => '8868a33a06ade20ca4d392eeafd9c798f4d1b7378599d77db359e35fa2c7208a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\AcademicSubjectPolicy.php' => '5f2919dd1521666b992bf3152a3855566085848e145d5f6a2b0dcca20205b3ad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\ClassSectionPolicy.php' => 'fcda622722364356ea19340b6d24e7136d3c2a383c1194343d1532bde6d0c2b2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\CoefficientPolicy.php' => 'a17d4ed4864f931f3002e14556832d940a99dfa649c67f8a5c28319dc216317b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\EducationLevelPolicy.php' => '4c579a3567a090e9a8aef830caa5d984ea64b2cac2eb060f1e2d176da485ef13',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\SchoolClassPolicy.php' => '0907ec0f18047daeba3df9c0d3e3eb0477210b6434f8bbddb1a342f740a4f686',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\StreamPolicy.php' => 'f68f7d73a4445fbed746853471401b30f3e09213dffa0bd05cad21f9e6b71e44',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Policies\\SubjectGroupPolicy.php' => '6245511661fd62bb83bebb4eea11f7faa19d9a7417beb3895f07e9885d19ae28',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Repositories\\EloquentCalendarYearRepository.php' => '9db025d9f517f59210255e2dc7333d6bb9570b49085dfb8afe4d4786cc12f9ae',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Rules\\UniqueClassNameWithinProgram.php' => '40c394489834a3636a5ebab4c2c898939514ed01da0ae40f086c5b5cfd8f6ef2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Rules\\UniqueStreamWithinProgram.php' => '1eb51be9ad638de79859b043e68ae761ce80fd4150d4cf73a9d82b9b7fa048af',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Infrastructure\\Rules\\UniqueSubjectCodePerSchool.php' => '4bd5c22c839a9265963385107a25ff2528e21365a45fb96ca8bd82cb18bd45e7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarEventResource.php' => 'ce4e9b74e726c622d41c69561afbd2179fb71103958eaf24f772f4b678996ff7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarEventResource\\Pages\\CreateAcademicCalendarEvent.php' => 'beea9fb4d4a11db9b6017de332aa46fd3048ee76196bad72ca7230f021eec178',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarEventResource\\Pages\\EditAcademicCalendarEvent.php' => 'd7b19db23957dc7744a9bed9e1780a573b37f2c757b3c0c20f32291b3ef989ef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarEventResource\\Pages\\ListAcademicCalendarEvents.php' => '40b92f05461932bbe54dccec2f6c6ba77c77255a4aadced3aedfc153930983d9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarTermResource.php' => 'a64f5b33eed42120cd8cfa5cdaa3118fcce0c42273e5e2b3aba3a3374a4a6a13',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarTermResource\\Pages\\CreateAcademicCalendarTerm.php' => '1a64cedc60599c92f22947d38547aa9f93ee62483fb41df51a75c1ad3d8c26e1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarTermResource\\Pages\\EditAcademicCalendarTerm.php' => '7c089273048091607f117766c9071a63d29156683478655db94763dfa8e43f1f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarTermResource\\Pages\\ListAcademicCalendarTerms.php' => '82ec58b976c1cb9086a62ec51a1045cfd9f2dc7ee5f311c696e3e7ee48187dec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarYearResource.php' => '10223eafe1f3fad80411be5f094596bd2bf78f2c48e188dec46efb24896da782',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarYearResource\\Pages\\CreateAcademicCalendarYear.php' => '01331f384b0ad9b31af8dcf74abf1a73e9c04d764d8b2cca1e4a04993aa8bc37',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarYearResource\\Pages\\EditAcademicCalendarYear.php' => '4f76a6c733bfcf4516137f26399498501addd999399c3575055dfd8a2932c89b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicCalendarYearResource\\Pages\\ListAcademicCalendarYears.php' => '69f5be3a4d8b76b036973834f1ccd8d68172575e9de7f5c84049281c524f44d2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicProgramResource.php' => '774c9db2398627f3c641afb72f16c4c18212f40473c9c1ec1d91a7ce2ad8a081',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicProgramResource\\Pages\\CreateAcademicProgram.php' => '727c8a0f4302f4de7804eceb381fff76c31f6f51a3fc3d3d6fd7beab09425b81',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicProgramResource\\Pages\\EditAcademicProgram.php' => '7028b71f16b3d3ad213624fd721c6b878682ee58bd6117671efc84bb1860aa2f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicProgramResource\\Pages\\ListAcademicPrograms.php' => '4152882dd669b242e2b9c5ac10df730b366c2a83c2c780cfd21d86ece633f519',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicSubjectResource.php' => '3678fc0ce3442c929e4ca00cfed430c0813640c3b7bbb579eda5a3f0ed9cef53',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicSubjectResource\\Pages\\CreateAcademicSubject.php' => 'fff438b2438815e5dbbd9af949177fe3cf506b62c5b61722f9a2188f3b4001a2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicSubjectResource\\Pages\\EditAcademicSubject.php' => '01e403face402c2c2bce66ffab4f9c1e0c7b6d71fa02b2d30fa02e8bf95e0b85',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\AcademicSubjectResource\\Pages\\ListAcademicSubjects.php' => '528b441c60e520ff56bb53b9132716944faaebe94735a13a2e3e590b77162808',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\ClassSectionResource.php' => '063b5ef03952bfcf2f52015576eb28a65f3efb6f4dcefdb8d1f55005b0520417',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\ClassSectionResource\\Pages\\CreateClassSection.php' => '1b56e054e04850b6c73a2f55f709dcf930464cd89d9ad8548299e516206943d0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\ClassSectionResource\\Pages\\EditClassSection.php' => '7d015bd12fb91b567c4076e3284dbfee789fffaa47eb5dc54866406ee321dd6f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\ClassSectionResource\\Pages\\ListClassSections.php' => 'e2b445bc731e94ee7d35f54a9ed506d2e8e9265c5e433f358efa4b3b9d0c0322',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\CoefficientResource.php' => '783af37320bc04cd7b1face539322ccc175350b004feb5e6af9824aada5c4ea5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\CoefficientResource\\Pages\\CreateCoefficient.php' => '33e89d268622a52cfb2bca5d5730547a6d5a9dec099897b1a2d4a44dd14c4eee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\CoefficientResource\\Pages\\EditCoefficient.php' => 'b1855e0e24e9203095a463122bfdbc9669d5170c15db1a72aa276e3f08867b65',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\CoefficientResource\\Pages\\ListCoefficients.php' => 'a74298f9639e33db8cc7d97d867b260bb0fefa4c2e6610a7c613b954b869ac76',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\EducationLevelResource.php' => '1161c8c5ca96c7890ecc09b11b5bdae82a1d6d22759e7f1de395ff9aca7a1430',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\EducationLevelResource\\Pages\\CreateEducationLevel.php' => 'fb7c3ca09d2ef53d35a415933f1bf291baa9abcb7a79255b0d63a709ad17605d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\EducationLevelResource\\Pages\\EditEducationLevel.php' => '84d24782242c48335bc3fce18baa11ac3b266d1d531610b01b50a31d136ab64f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\EducationLevelResource\\Pages\\ListEducationLevels.php' => '14a6288e597488d43347ba0d59176754b255f88aac4cf061101dc4773c2f0ae7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SchoolClassResource.php' => '4d0b5df919fa164cb171cac7478644c52c1b0b55aed46cf4e2ac3c316f610831',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SchoolClassResource\\Pages\\CreateSchoolClass.php' => 'd805f426cfd932c4200ad24511513d7d03fa8e65e1c571659534e7c372e109e6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SchoolClassResource\\Pages\\EditSchoolClass.php' => 'bc7c3e35c32868462b61cd3d0491731c7345a0e2aea596e9f0bf8f9d8ff3cdd2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SchoolClassResource\\Pages\\ListSchoolClasses.php' => '45e4353e7a861dcd571c160fe75d5a66068f9588dd7f47c86fcebaa03fcfda60',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\StreamResource.php' => '9b9844202d4ca3e1578e3b9a6fb4319e90ab173161a1d9333d1a8d3c20a90f1b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\StreamResource\\Pages\\CreateStream.php' => 'ae97406e319ba0baf815749ef0078e2283ce79799abc164696da8c6a01d4ffbf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\StreamResource\\Pages\\EditStream.php' => '0242f7b07de827158fb8cff3f47983ec2caf5887ff17a19b2bbdf595163719b9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\StreamResource\\Pages\\ListStreams.php' => '5f61af39006d7bd07350fb2279a0bd07110f532c56c7d18c3893660644c8bda7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SubjectGroupResource.php' => '8b16946098b0ae0b88adde64063bce14cf678eeb676b48796f5aa3da8f333eae',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SubjectGroupResource\\Pages\\CreateSubjectGroup.php' => '8cfec6df07647015b2cecc40c7ab849e0180c13ef83c26a992733f1a1f2c22a9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SubjectGroupResource\\Pages\\EditSubjectGroup.php' => '9c394992aa43e8a82063581fc975c84c6343d946f63ddc76d47cf40fe44d21b1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Resources\\SubjectGroupResource\\Pages\\ListSubjectGroups.php' => 'faf43fbf00a54e1ee6d5cc7837f195ea68a7b77386690205c0af0f1978618433',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Academic\\Presentation\\Filament\\Widgets\\CalendarOverviewWidget.php' => 'b6394287a146f68d626ea1b75458164f4a213e0c573cf4c2ec8586faceefe62f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\DTOs\\AiConversationData.php' => '3e728c5104542d9307793fc44b3de3ebd0991d675f5aece6f9a7babff8ba6b55',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\DTOs\\AiKnowledgeSourceData.php' => '38589e2bad43751fe66a7e371f10176571008b58e36972113ffa6746ad38aaf2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\DTOs\\AiMessageData.php' => '171af0e13fa2f1ce2390dbe04fba03b4e7c9738dea741fa904b9286af55d34d9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\DTOs\\AiPromptTemplateData.php' => '00c4739192e762e5a7194b2573dd8ccb8274c23ea3df1551f2324d4034f2fbbe',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\DTOs\\AiProviderData.php' => '5b821f4e003f7dc471ee4da84a249e0b47c9af1851d8f21e85099aab622d4afc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\DTOs\\AiUsageLogData.php' => '2de94b00478e27bb74401948d88cab57b842fdeb115d7c3ebb70415c00d789bb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiConversationStatus.php' => '56a9d208f034fd057d45ec9b2a2938e870f0a6f19e69ba1b0b933c4e44bbc2fb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiKnowledgeSourceStatus.php' => 'e4a4c9c87cffbf4a8d223f1fe80a66949dede835d374f97692bc1d33041869a9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiMessageRole.php' => '0dbbddffebdebeafbb82862ec76df4ef800380cab7dadb184b3bd652ec34da66',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiPromptTemplateStatus.php' => '499e2d360534b92a0a972ca319214e439b18da608a43ada4b95085376ccf68fa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiProvider.php' => 'eeb2e88afb0fa61a8d34d47049692a08a224c4806957528f34c72b4fc1180810',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiProviderDriver.php' => '2f841b75be33ff4b095afe444c059943f1f7db935a3f9bf1ab2482f200c0aa3c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiProviderStatus.php' => '77b97f32a4e50ee3cfebe9be84e3545ef2de36416c33a4c5aa135e2a230fe379',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\AiUsageLogStatus.php' => 'eedaddb63c2d227960b6ffeafc85fb77b9f6a96c9114aec050b3cd4be9821ecf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Enums\\PromptCategory.php' => '89c5f0486578c8ae71f2a25de232fb6120d72dfacd12b0af17a99c84bb901be8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Events\\ConversationStarted.php' => 'defaa222922a7ea402f6fc286db425940bd521a7de61b31e66c4dba27b49e679',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Events\\KnowledgeIndexed.php' => '814352408b59d59f30cbdb1b83a69af6807f0a2a8a11289bbc0fb8863d3fdccd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Events\\MessageGenerated.php' => '45a468735c0e4915cd130fc99bb9cb0cac01966f8d0aee1b13c68f6db03f4743',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Events\\PromptTemplateUpdated.php' => '152844f42bf4ba675ed73053ef2e8819000e18dc9bf76a81d305f26aaad44bd1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Events\\ProviderChanged.php' => '64187e0efe24f4f24ca1e417ce36ee8982221a309246fbd52bec3a6dbb3783cf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Events\\UsageQuotaExceeded.php' => 'ffb4af4808b5b51d97a594254ace31e4bf2d52a9c063057c1c1c9068f416800c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Listeners\\AnalyticsCollector.php' => '1a8262db629dba3d537e0da2aa9f36fed0a64e453f15f3f55c8ba347da0d12a7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Listeners\\AuditLogger.php' => 'f5b2d0c7c8e4468bf7044b3b6af5358784e19284d06e15ef93205c5061db059a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Listeners\\CacheClearer.php' => '3b477420a82e8f3aad17b33a5d8398e760e47d3591dfd7dd77365b76b734c69d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Listeners\\NotificationDispatcher.php' => '80e923d58f37bd815d7694ff7c453ee3efd196f527248496aa3f790d30240dbc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Listeners\\UsageTracker.php' => '1418ab11f19974e3f712666145c8cf3d03415723cc9818946e3901622003c352',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Policies\\AiConversationPolicy.php' => 'b4c06d954075c24d99ff9f5c994a1a9e337df47cb90c14b6901393df31885aba',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Policies\\AiKnowledgeSourcePolicy.php' => '1fc9aab3064f458c97b33745c92a391c2179a08dd837f16d8c0dc98d9881ab6a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Policies\\AiMessagePolicy.php' => 'be009317c91e32d0088413ec871b420531729dc66d411e27630706fbd0a8a5de',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Policies\\AiPromptTemplatePolicy.php' => 'ae1a68a0a1be88665e266dfdeed4b8ea39e4ea51fd56408b23c31ab2f3b00731',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Policies\\AiProviderPolicy.php' => '0b0c5834198c2ba1eb1edcf304f5d1c7e1e409dc0f0443bfc80541c66d482bbd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Policies\\AiUsageLogPolicy.php' => 'c99c0ced34dab4f5dab0e5371ea85bdccebbf74c9c81071434f9e51d4721d353',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Rules\\AiConversationRules.php' => 'facc7f16d8d159de4f594aa5ab8b85cf115e8dbe8e8010c153ccad427dbfc891',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Rules\\AiKnowledgeSourceRules.php' => '2087265615efa675d99ae16ed58d0a91fd17e752a94e6cb8ff9aeb029a1f4a39',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Rules\\AiMessageRules.php' => 'd879fb7782eb01edca457acc603c4c457f1d6acd48f651eae087d10555423fe2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Rules\\AiPromptTemplateRules.php' => '6994da03b14f2e2d49e2c1d0614b55facf2ee2570641f4e4052b64b949a1ad3f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Rules\\AiProviderRules.php' => '9d8e880f35789e425d3c35ef00d3eefc9f07fa334fdfd60bac3f50d92d3539d0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Services\\AiService.php' => '5d70c4c76d7572b1a562ebae73b47cb6d165661a8f39fc901576d0fc3617ef1a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Services\\ConversationService.php' => 'c474f6b627fe7526c4b8515cd30aae1866726855db18106e7ceb20cee68c1fa6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Services\\KnowledgeService.php' => '25b6d9782643da6cbc824ca2a69ee47ef534f10027cc6602e1518a5228a871c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Services\\PromptService.php' => '0a52770fa49540b88066f18c6522f825d758169afa4c60d71d06b49d3ebbca14',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Ai\\Services\\ProviderService.php' => '9f9cea5bcd01733a3b84b807641a488104fedd1cb60f644d430530f256b67923',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\DTOs\\AnalyticsSnapshotData.php' => '7c4568fa9db26191b17b5b4da2bd0a67186d89d6896c9d80f36e4b444aa6b3d8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\DTOs\\DashboardData.php' => 'bbcc79e85d76fe8c213f470b447cb39e3be580d1c0b02278cd5bea37b8a7e1f2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\DTOs\\DashboardWidgetData.php' => '4b63a217ecd13078c2eb2874093a50cab898cc492ed53b98f852064f3a57c410',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\DTOs\\KpiData.php' => '440f5ae0e3f90590143e86583e5a466d6d36a6ede4690c9e7c442db621729620',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\DTOs\\ReportData.php' => 'e141ed35bc8735c9b81ba5a4659b598cbf353d046790414f0254a068b323975f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\DashboardType.php' => 'b13ddd0a0d84c44158b8823190e9048a46075899722d01c44979d8f153c5a004',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\KpiStatus.php' => 'bb6167b0ba769eb7150bd2b601f20ca264f4b9db5fb5ff1c453b5379d98b3fa4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\ReportFormat.php' => 'f613db68b751db493205061494e2d9d70535332c159110d303d8d58e666ed21d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\ReportStatus.php' => '2b328f6965275655f8030e6dcb74c5f6e41937bb73d569efe88f85b21f3bad7d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\ScheduleFrequency.php' => 'b6f521e11881462a3a7c735e4b263bbb9362d243d4c59474158194c444ecee98',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\SnapshotStatus.php' => '34a7611dd2a836d5c7e029a8486ec4bdb549a7bd878e954a594de34a3d3fe464',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Enums\\WidgetType.php' => '1f7d557bb16dc9527308d9fb956784dafafbc21c0998b5c9b3a5136264a31c45',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Events\\DashboardCreated.php' => '08a797417f9fb349aa78e6b94e3bb75d8c1428946a4086f4afebc23d6f090115',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Events\\KpiRecalculated.php' => '349085580fd1b8d40a2946f69c68ff4a9b995bebb52ddbc8f3c092426acd9fd3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Events\\ReportGenerated.php' => 'b8d09bf90ea5e0e79fb54bad5fe3e33bbd31b33f9eaa07347e33f3830c34e214',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Events\\ReportScheduled.php' => '59d71d32d45c4bbf184d5d2a84ff2468de22102191fecdb7f8b978c8c1d32c06',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Events\\SnapshotCreated.php' => '7cd06569ffd0b1dddabfd2dd552ea9a350e56e13dd3d060aa70dba83917c396d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Events\\WidgetAdded.php' => '2e7ec49b0ca9fec1247905cb31dc655ceffe673a8d8ba61462db83572a62a5f7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Listeners\\AuditLogger.php' => '1e005b231485d1b8368f5e80cb8744de3c045e9316b5e510addb555b40603ad8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Listeners\\CacheRefresher.php' => 'eb76eb0af9cace35bd5cf86bdc6ef95ab91d5d2cadf36efc8c82fe033d5f0058',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Listeners\\ExportGenerator.php' => 'b15ebdae64b630f35a0d7e406b9805739411594c1c01d335daf3247788b18e9b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Listeners\\NotificationDispatcher.php' => 'd2554984a3c24268997e214a3d7a648415debfbb5655d683e488735f0f8e53a0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Policies\\AnalyticsSnapshotPolicy.php' => 'e4f95e536ad38d1b61b54b3310f387310a21127e578d92adb94c2f241f0c7d6a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Policies\\DashboardPolicy.php' => '44985313b6d2ad6dba7ec116326aa80500980747a71b2cc2a02631a000abaef7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Policies\\DashboardWidgetPolicy.php' => '7613d9092a25090ee49e8f6390a3acc0ca226676a5030ed06fbe7221744754e0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Policies\\KpiPolicy.php' => 'a5a3e1feb1dad5fa3c8ad7411751fe430affb700905618fba39b37eed691eccf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Policies\\ReportPolicy.php' => 'ac74016a0dfcde5ce01516cbd7766c661eb59a01ab41932c5636a581bb2a335d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Rules\\AnalyticsSnapshotRules.php' => '9e8bd73fd1b7a4bdd3aaa8da2fbc805c82fa24561cde5db443333d5a90680d8b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Rules\\DashboardRules.php' => '5cd28aacc193d15a5e7c4e71cdce63a1654664db815533ca0eaeb2de1811a1cd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Rules\\DashboardWidgetRules.php' => '518079848bf10074184835aac0a7e035bdaff36354802329f451d06af527a96d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Rules\\KpiRules.php' => '4b8caa710452eca243a60e11e33758857e587bf2c94e6a916c3e64eb1de84f09',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Rules\\ReportRules.php' => 'f89aaf64833812c9f3229fb54d2ac757978931992593ea23e0244c8f81faf427',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Services\\AnalyticsService.php' => 'fcf90e8f5685060fd1a6c961a6843ce212015f82d4375d1142aa18517d4da724',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Services\\DashboardService.php' => '25575a9e2aca4406e25c64e1701f03973561acb113168f4ab6e2161ea53b2952',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Services\\KpiService.php' => 'c8f3de5bbabd7f17a664e947701501c9da10d34ac01556dc25c9c32e19b3e5a4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Analytics\\Services\\ReportingService.php' => 'fff7fc153bcdc76415a66b5781162c307fbe306d289debbd8f718b4d7c2d780f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\DTOs\\ApiAuditLogData.php' => '690e7efb04b4946f4e2f2a94b7ea015d2f509b6d1fa16cb63c3bb7a53788c934',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\DTOs\\ApiClientData.php' => '80cdb381f077297262b52a1b14d1860e5f93737c39935733db2e2fcee8f0d56d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\DTOs\\ApiTokenData.php' => 'c868f265e9fb812feb6e44357e08644092bef07d42b219201af409cb8c4fc3c6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\DTOs\\WebhookData.php' => '4df72c34b25b51a491bf77d321490cfe1f41486c58bf7414b03f06d076d24e4e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\DTOs\\WebhookDeliveryData.php' => '23a3c80674f3bc190a93004c21d6ce66148d0a9ee46d0ac6d5fbefd1bdc29904',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Enums\\ApiTokenType.php' => '67812761de4315dd341669e8356607e63c1ecb968f22c38574832d6c565f57ed',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Enums\\AuditAction.php' => '4cd6f1eaf44ca8d75a5e5e688a747b07e3a725c2b0271b8a1b7e48eef713db98',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Enums\\WebhookEvent.php' => '74f5dc49693e535849fcdc267e30b099f40d49fb2266d7da98c4dcde5c8e9784',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Enums\\WebhookStatus.php' => 'bee32055f4147556a990715200a2a03ce01b2e1bf47e01e13d668a5cff0a882f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Events\\ApiClientDisabled.php' => '446d8a6fb567b3b48922c550ecb6db6936c255fbc449299021690ee6e5d4c423',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Events\\ApiTokenCreated.php' => 'c48118c7b3391856b48e8cdaa9d980a84bc5d645584c668701711c3951be1bb4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Events\\ApiTokenRevoked.php' => '0daa7e36d8e11c7e1c87128cfbfce98bf4cf8278688985ce2f8b97e87be58182',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Events\\WebhookDelivered.php' => 'c8d68efa2b964138b1590c31eafd6059bee561ebfab27f33ceda9c90561520c7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Events\\WebhookFailed.php' => 'c9064065e80178e9764b6d96d7758f5eb8cfdbf91e3f9b2be5c81284f8298582',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Events\\WebhookRegistered.php' => '532047224eff41f37db457873f9b9f1962c83655d41c57abb9a9205b869743bd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Listeners\\AuditLogger.php' => 'eff04cbf8a09cde692d63d465588ee5f44f90aa738b532bb5ef689a51538042a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Listeners\\CacheClearer.php' => '0effd4627ab9c2bae030c827305c33e4ef9714d936724710c58c6bc05ececfd1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Listeners\\NotificationDispatcher.php' => '1154ede6a21a1ab98137aef0701ee24a63761dad001e3e7be53f05c81df25769',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Listeners\\RetryDispatcher.php' => '0bb54b198ea541a009bbe7d5bd7497d0901b6007f6d4ae7b2345578f0f28c96f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Policies\\ApiAuditLogPolicy.php' => '72baf8e7d1e8558a479a29173449a4d5ca08b804d2580d861d1fd340e0f30243',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Policies\\ApiClientPolicy.php' => 'b566347ae50f9ad815127846f4784b9c654272b8a9819c952dc37b9b678fba57',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Policies\\ApiTokenPolicy.php' => 'bed6e02a2ea2b76d2db06d1efebc619132c830f2acd9b1758c4afa39f3f2e028',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Policies\\WebhookPolicy.php' => '0df26d98d8bc9b3ec88f74eb6716ee3dd34dcc996e3cff3f51672309a349828c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Rules\\ApiClientRules.php' => 'de1f42b26f127f275dfd431e45ebbdc08b64778f14e644b7f88504fa73cce177',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Rules\\ApiTokenRules.php' => '369243fcda47f27e1ba42bc9d22ffde66e001901ae8027367597c76897953218',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Rules\\WebhookRules.php' => '4a142e488e15ada00f1841520b36f54b5f3689031f9007983541069db1cdbf3d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Services\\ApiClientService.php' => 'b2d9de7ff954d21e2ee600f844ef35b21a44a25d895ac3c6481cb5b5ce22bff3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Services\\ApiTokenService.php' => '1c1c431bdddb0cb9915915cb7f01cdec46ab87356413c7d494f5e49b7e6ddffa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Services\\OAuthService.php' => '191861e724b74288860fa04d0876ec40d4aa1a69df21be1b53bffcd3e1294fa9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Api\\Services\\WebhookService.php' => '3ac04a1452d37d1798f24d2191f9fcf5fdf1e9719720823f271d05eb17972763',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Attendance\\Enums\\AttendanceStatus.php' => '0b238809c6a659c053760d98b0a978e6c646a433af8b3e91bf263db09596baeb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Campus\\Enums\\CampusStatus.php' => '3a76863ee05edd9c771064c37f471477b75cd0d8517d61fbc0b31c91ae72c5f4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\DTOs\\ClassroomData.php' => '8fbd6e752cf61acdc6b4b27acd6688dd091bedfdec092e80195f226c75b7b978',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Enums\\ClassroomStatus.php' => '9a943701ce6676134e015d864a8dbe73b22efda1969fcad0359f64aa0c9cafb9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Enums\\RoomType.php' => 'be821d09b4b2d12f5bf097bc5fd3132e0ae1ab9a71d498abcc3dd281fc9b23c1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Events\\ClassroomArchived.php' => '338e23059cffabaed32d4084338c067d08271172cb65f0d3c301b48151dfe6e2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Events\\ClassroomAssigned.php' => 'd1fa226e2ce4afef4e0f18bd5036188f7bfbd5f4fb409ce6eaea0337872eaacd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Events\\ClassroomCreated.php' => '4b7c278df99e0df96a2b2e11e6d00d98ae12c52675d8b6578efe9d1dcfb5a8c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Events\\ClassroomReleased.php' => 'c0bb9a7c7f504dd315df55e9a89a2a5aa916948a66fa10cd7a81eba6fbe185c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Events\\ClassroomUpdated.php' => '60151037569335200bc08c75105c35e23337d0ff1506f538a0efd115a20fb9fa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Listeners\\LogClassroomArchived.php' => 'a9f53c3524ba0ef101a66e42c3bcc51c693dfce63dba3e89bbd8dc99a72272a2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Listeners\\LogClassroomAssigned.php' => 'c8ea54ad6d5583d79cdd9390bbff96d2c422c020b60819630d49337500b819be',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Listeners\\LogClassroomCreated.php' => 'd399ba76e106726a24dfed0b7e7e33843f9a46a161c41c13c46ec299a0448c38',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Listeners\\LogClassroomReleased.php' => '1c49e3d1f8f6cd131e3d4c504cf0fbbe05ee7a8a91cbf84224db35dbfe33f68b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Listeners\\LogClassroomUpdated.php' => 'cd5bbb6c00fdd38ae3a37a417f065edea96485ed9d809f67de6a070892419b67',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Policies\\ClassroomPolicy.php' => 'cd9d5b090042eb32d4950c9b9e71ba59410653888674cefe866f9d7d19081d08',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Rules\\ClassroomRules.php' => '70686ee009eaf39beced0f6147105fd166f29d7f82a2aa1ebf31b1a63865fc94',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Classroom\\Services\\ClassroomService.php' => '0d4df9d2d968c6aa36d0eab2c3db15b24eda9160a131d92769230c3718fe33f8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\AnnouncementData.php' => '279d62b547b1877746997f69a7f0a3412bef755466959da27d713987d007bd13',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\CommunicationChannelData.php' => '3b40926de2cf4396df7e09b7c5a563c11faf09e5d4d33410e99bc7b821d7b019',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\MessageData.php' => '1a928ef102f125f8142f3e534dc60879b9100f515b0f9be278d7b341de888ea5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\MessageRecipientData.php' => '66ccdab88d159092adfa92d53b5057afed31a4619350460aaa1bcec311dd919e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\NotificationData.php' => '46aa97d50ad8eda025b6b58b16684bf3e02b632aac744658abf197eaa2dfc561',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\NotificationDeliveryData.php' => '955d5092e654c78b5fba3bf50abadeb50ca24671ea25bf3c80004b2eb8d99a13',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\DTOs\\NotificationTemplateData.php' => '74712dd95f35d18feda1bfb6ecac802b78cc482a93df19426ca4bbf7e4acdba3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\AnnouncementScope.php' => '947aa0309ef82a30d5bd93fc08e889837a81bc0369491c63f8229cb7d88fdd58',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\DeliveryStatus.php' => '38cd4e29712aecb34a4eabacc90e62655c3923db11875bea626e756a2282cba5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\MessageStatus.php' => '2c72a1b08923cfb3f522ef46c3f764e1a175c07de6919cef4c34eebea9cbccb2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\MessageType.php' => '68f39ac8a73dd822c202438a93137f409c33df59db5bfbbc1ce6bc073c3c143c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\NotificationChannel.php' => 'e841cadc18efb08b844221b3abbcb8be3d25f46511276702d030a619f77a6ecf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\NotificationStatus.php' => 'ece63e50baf804a96807215192f335fe2807d502416d0367511ed016d5c89847',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Enums\\TemplateCategory.php' => '92cf0e389ed003e08184c701a488132048699426cca5abedbabee4d44c4e94ed',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Events\\AnnouncementPublished.php' => '07ddee8e44df5502f43c6242a16919171266c27f4c68cdf1d99c9f6918ae186e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Events\\MessageSent.php' => '9302f3614579e5695afb19848527774d8658a65ddaebd6f3468b350e76f4badf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Events\\NotificationDelivered.php' => '10ad3af6c230323a0f20d1c22a06bdd363a6fcc5005f58c680a02a1750dcd56a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Events\\NotificationFailed.php' => 'e0b4c0c14063ae3e3596e5fb561d0993bed0e0e7566f4c0d68e7c04a3675965d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Events\\NotificationQueued.php' => '98910fdb5e9a62671e6d0c334b676a1d1c0d0b5ff1270e730d3d7453c528cd9b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Events\\NotificationSent.php' => '5f6483ebd35af82b854b086dc4edc94368f1277d058866ee37ad54180ffd68f9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Listeners\\AnalyticsIntegration.php' => 'ac94e3d6cea3bf0562828e1fdc31eb36e7dc0098d1cdabd87a020d159108faec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Listeners\\AuditLogger.php' => '652972674b5a18d0830a8327c29e2593f47c012edba7bc26d2620d50e028a578',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Listeners\\CacheClearer.php' => '59227c0e4cef90809c342ac249fc9d0b87ad5fc68d220b5b31860841e7f8b0c8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Listeners\\DeliveryTracker.php' => 'f96a7bf11d432bfdbeff8e5aa72f6ac404cc61fcb5ab15f92c8c48595a97cf31',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Listeners\\QueueDispatcher.php' => '5126a8bb99059a14f73b8ed669812f469f1a258efc18b264b84a734148d63981',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\AnnouncementPolicy.php' => '7e452b674834679fe5549f1f1f392d980d22c2bff9205f7fd5af95bf4fea2177',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\CommunicationChannelPolicy.php' => 'ab61f6430c07667b1015788be41fe9a8df5f6cb3ad568a756c206b3121401546',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\MessagePolicy.php' => 'a45efa3e848cd27907e15cf93bfc9265bd146506107e131352fa7ef2a0721a74',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\MessageRecipientPolicy.php' => '999747fe5f142cf7cd32fd9eb40aaf15ac5ed0b437a9f74daf66dba5675d105c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\NotificationDeliveryPolicy.php' => '1c87276ef7fe4d289e8262964249b400742bed0b7a2fd6c46d9a60d0adb768c1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\NotificationPolicy.php' => '2c8bab86ad8c7c6e17ee95bbd47d634775a93d512af266d10d6699d92a0f0e2b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Policies\\NotificationTemplatePolicy.php' => 'f42887139919760eeaf94f24da2a79b85fe35294148b5ca6abf67c9d688ae54c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\AnnouncementRules.php' => 'e3d667719a1438d599d4efeb54d7385654012b1dcd7f123e9a7323648f9ad367',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\CommunicationChannelRules.php' => '54b5d9eb6a108511cdfe4e064054835fef8e67314a266f03c700a07e001e9db2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\MessageRecipientRules.php' => '03418a0c12b6aa814a3f2b9ae7f8dc7b0c2de9bcab58785704b1a4762eb5157f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\MessageRules.php' => 'a70b43d643bc700707681cea6669979a8497040639a24e843db7ac25a84d4f1f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\NotificationDeliveryRules.php' => '0a184eea1051a60c12d6b1634fe67d4574474a2187b958cb61fc793afd93c65b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\NotificationRules.php' => 'ea91aefcf8ef7b460ce3e73f55ed0aee04672b0d49f5df4544c4f4fdb10bd98f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Rules\\NotificationTemplateRules.php' => '9bc2f87c1a4ceb0ef468384d756c4cc3af7e0dc8ae7f252de0c98554060f7cc3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Services\\AnnouncementService.php' => '5e9c0833b61b58af9a3b5a195e599962771959f3b8dc901a7e11bcda33284d04',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Services\\MessagingService.php' => '93f5ba1bead93e033bc02eee522f02da8d72f86fec71b22d180b93bd70f04e55',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Services\\NotificationService.php' => 'a0ead1685eec02d5cedb99a422aeb2761ceeb0407894b84ba1e661c4e69f3995',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Communication\\Services\\TemplateService.php' => '45f0d43cc2e5c3fa8bdfaffddcaea0c7d3712cec0b2d3dd51509c254910e1774',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\DTOs\\DepartmentData.php' => 'a4726bb6344ce41995e4f00b55f975c80fc33c03a2a4e4c7410c6e79970b95c2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Enums\\DepartmentStatus.php' => '51e252c253dc76541cbdd802048c3dda93dd12016e68c5095b15e89953252746',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Events\\DepartmentArchived.php' => '3852fae7fb7a87f3d8b6cbc8be3423b6073dc964d54c7e6ad7ccedd6ddc330e9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Events\\DepartmentCreated.php' => '34a7e33b83d990a6efad7efce2ab8597975ade7de7b788f1738fa1c6b4314665',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Events\\DepartmentUpdated.php' => 'b72b7ffddacb0b80c96b6cff9f579cf79f2f3abac309286b68f373f4aeca9e93',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Listeners\\LogDepartmentArchived.php' => 'ea52b6590816e714ea38a988eec2f8115b10b29538ff7d6e7183be8f443782ed',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Listeners\\LogDepartmentCreated.php' => '1942cb97719eff9e915bc3ae200da64b52e70fb124e6ea1ec494c82ae733d9ee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Listeners\\LogDepartmentUpdated.php' => '4732d902f160bec9a270d9324f7b7ed3ea8e6a4f6c4390c999ff9ee3ccb06c03',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Policies\\DepartmentPolicy.php' => '6cc4b4d8289e3ce89dd16ef135765867988d1c4f63da2fef21cba24d23fdba0f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Rules\\DepartmentRules.php' => '9ece95073657a70c185b2e5c8c53dc0cb5bc91686d2ea2d621e3aa050d3add84',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Department\\Services\\DepartmentService.php' => '9f1ba57e3c524014e7996827f5e0ce28c0cbcf68a1eb6cda975139244c6f28e1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Document\\Enums\\DocumentType.php' => '250f532beebbef5a57575b419702a98894d8167f34f5de5d997bc113a2c9fe15',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\DTOs\\GradeLevelData.php' => 'bfa282aa61fb9326b289ec49f3af2774ff09668b0700df2b2bb560382fdbe877',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Enums\\EducationalCycle.php' => '69494853def7642812e9221d68e1a74019745b28e7e81983e7ec6e37d6d8d95b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Enums\\GradeLevelStatus.php' => '7e81897649bfb9f8433e83d811ce05a541d4934526f67743b79b0991f109ab4a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Events\\GradeLevelArchived.php' => 'dbe023870b236714915d8ccdef4670771045cedc1fd610c1d24aaaf43704fd90',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Events\\GradeLevelCreated.php' => 'f5deb10aef24a89fb727d693729c275a5893ba4bb8a52a9800610a8369afc4c2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Events\\GradeLevelUpdated.php' => 'ceb31dce212e03106459eb5f15fbd206de81d467c40dd23de3b0fc6f7b243ba2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Listeners\\LogGradeLevelArchived.php' => '956bfc0e0487a9ea934487079d6674b6465d7a78566e0671c449f843066837f7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Listeners\\LogGradeLevelCreated.php' => '47c984605b80f983790ddfaff2da53ff50773e3cdf45bcb28b9adc38298b0e8c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Listeners\\LogGradeLevelUpdated.php' => '49ac5c19f9d470e20a601dc08eaad9630a688f38b18289d5f119a9f38c1a5f47',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Policies\\GradeLevelPolicy.php' => '57b1baaaf903c686e2894749058c49859990d656743bbb93e7ee0a554433b092',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Rules\\GradeLevelRules.php' => 'a1bdd21113102c03fce6e34c4091767ab29bbc643cd46ea2ac0de9a7bc336346',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\GradeLevel\\Services\\GradeLevelService.php' => '58bc1ac68c9183e3b302088ea1e22ed2ddaf568aa76ac3e7a0dd3ac8ce9102a1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\DTOs\\AssessmentResultData.php' => '35b6f74d38cbfcc916d7742e7e24f9a7c4d8fbff0bf2acf55ed82f91c54a95cc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Enums\\AssessmentResultStatus.php' => 'bdc1bbc177823fbf8bb4060d557fae109cb5a19dc011f3bc8b3d36c9cca7a873',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Enums\\LetterGrade.php' => 'eb9fd1ad9481f4cb88248b935ea35aac1717c96d57a624974f9e5b20185e55e8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Events\\AssessmentResultRecorded.php' => 'e83332388fa24f28c115a641319306625e24b535ac4f6547a921778067116213',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Events\\AssessmentResultVerified.php' => 'fb1b4070bc9d72c7949e08428144c316b95b2a33e9129f093f02ba3f0470063b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Events\\AssessmentResultsPublished.php' => '95e2f4b7288875d51350196b4a57aed557cc7d730291f9968fe7717184acb403',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Events\\GradebookCalculated.php' => 'f811946f0ce00b51bb2ceeadd9246c59e237e6f6209d197a808d0d4ead9edf92',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Events\\RankingsCalculated.php' => '947afee7054550bed354fe58267eafe760cf9fef0bc0fd75c45b680193b93c51',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Listeners\\LogAssessmentResultRecorded.php' => '8688072804d3c10560a3df6911249884ffcca5c6e0002afe76ead885aab7c886',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Listeners\\LogAssessmentResultVerified.php' => 'c5ebf7be4b7e1d9e7cd2f13e9128b2def9c00911bf8cee856b2b983c6de1ac53',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Listeners\\LogAssessmentResultsPublished.php' => '0a188d3f42b0b198e8f30785ad7fec34e6b3e05e444ca2ad4f01af2e9d5edcab',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Listeners\\LogGradebookCalculated.php' => '2723ebc7223735168f2d26a2d90c31ff32264ff267d82e30290e7a70e5311caf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Listeners\\LogRankingsCalculated.php' => 'be4b7cfaeee37ca3ef081cfaea3c98f3308cf6584cb7f0406c06eedc0da097ca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Policies\\AssessmentResultPolicy.php' => '8488e85e7ffdefb938eefedede524ee409d8be39030988b2968a88c9db9b9e4a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Rules\\AssessmentResultRules.php' => '39a7067e83393e30e9f3de8e1cdc19752aff647c1719bf316a94c002a0739db6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Gradebook\\Services\\GradebookService.php' => 'c8639246ec27f92957f0eec163deda690117fac3993783d9806c75a29e9bef32',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\DTOs\\GuardianData.php' => '9271e137a8a5cdb1df957ae07fafecde39948a8ed3db5c60711cbd74655e480b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Enums\\GuardianStatus.php' => 'b9d3b60afc5dd7b30dc2b2be5842ef6508e31217cbfa0d03dfe4b7823900c650',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Enums\\PreferredContactMethod.php' => '6d990d8702d73ea95592667204206ccbddc1717d38e4e4a46fde9dd884209305',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Enums\\RelationshipType.php' => '94a13f8b8d8ac3d2f98ab3495bf1af241bd61a919fcfee82c7b6e9e00326bd33',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Events\\GuardianActivated.php' => '335911def0d48603045a9d4decfc64d52c436425b17ebea97fed380b9ce6f506',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Events\\GuardianArchived.php' => 'b25ff8b0b7b86997dc693e0c64d04133e63663734344291cb74f6556eda0d905',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Events\\GuardianCreated.php' => 'f32c65c85fa9acda4cd5861949128c193b001500f9a3c9909d4bda5d867daea0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Events\\GuardianUpdated.php' => '5faf03c9e3365d6f6bbac22932a601c6feca8735b8f4b0ee5bc7a08d78e47764',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Listeners\\LogGuardianActivated.php' => '5416a2597531ac1536b84e7b18200426ec514612e3e239d912338e71d7476636',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Listeners\\LogGuardianArchived.php' => '580136eb57ccd2881efc58c4be52929a6ce2ee4d177d420354f31592dea9287b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Listeners\\LogGuardianCreated.php' => '5c5acabe428678305dee298fda366982686ab458138a58d9f283272b65f4104e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Listeners\\LogGuardianUpdated.php' => 'b8dbc59c77ec1c1d64f6be0d12475c6d9a67a635c9a665c2d4c93e68d011965e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Policies\\GuardianPolicy.php' => '43ca01f8a44fc7364df931bd4249dc647e75a638f9b814cfdcf5a83c3cbcd4d1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Rules\\GuardianRules.php' => 'acf8514dd649a66e6083da9e6f77d8c41733538a640bdb9aceebbf7336e06e9d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Guardian\\Services\\GuardianService.php' => '9baa77c5482f6745623b06e5f362bc1a4379e6132e534ae22e01c8d424872e3c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Incident\\Enums\\IncidentSeverity.php' => '45b220d20d4c2eb62e365f33eeb463c347d733b141fc947d3aef835a402aa5c7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Incident\\Enums\\IncidentStatus.php' => '24f654c160af4edf0ebec75975eb3588203308c731f28fa24d2480988acbb160',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\AuthorData.php' => '70ca60e2c8ddd66bdd44f2fae4d51c893a713081c8837f071fa55a534619b360',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\BookCategoryData.php' => 'd392c068b2a4d208c239aa4aafe43dc66034363a0343788a5a376e8c8e70c750',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\BookCopyData.php' => '57ee20dd06d99d3ae2fd54b8a58648ab7c1960a73a0649e728106db0e517aabd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\BookData.php' => '26883495bef27c2d114008596a77a538aadc01a785c3fa912f5a79fd642f7eff',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\BookLoanData.php' => '9836ed2f6f03f343e5a977328a2363429a0780280ecb8b2f551f5d436ee5f634',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\FineData.php' => '2c42a6960f130e80a04b3b8fe28f30b940bb967bc6f684aa3f6d3b9df75b82bd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\PublisherData.php' => '397c3540bbe4a68492b24fe02bc5047133b9aba5ee9a2c38b4f48d4c6f7c5ca9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\DTOs\\ReservationData.php' => '59b8c103c5268c7c8d8506dd8086a69fdeb4fe14e7b251dd26d71e1b9bb08e6f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\AuthorStatus.php' => '5156dd566ae1cda3de4e896191f895a95cec258a35fbe700192d1f8443caeecf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\BookCategoryStatus.php' => '4a70f1d8ae39bd4c8d5fe782698f6b2b47e92266cabdd1fa7c6e438e97490586',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\BookCopyCondition.php' => '2e7299d4c83465eee09076e41a4d22fdd61ea77dd086fd0715f2426e192b6807',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\BookCopyStatus.php' => '7ce7fcb9d171f7a21dec18811473442c72d4b758714570fdd476236ca4db4b48',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\BookLoanStatus.php' => '8b4af1706de1ba57eb379475a41a1612b67ef5b9bdfeb9a1e99465d99fccd170',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\BookStatus.php' => 'edaead728839bac22e101c485395d5af5af0d7a827baab8126caf5992effc0d7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\BorrowerType.php' => '53305d23eab2d5405df21e37777a5fe8ed591d4ad2392a6108b6c3de56b9d1d9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\FineStatus.php' => 'eda9872c940cb08c2d0694d0cec4326fd28a430275e93ea8e3d69cd9c0dc5d35',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\PublisherStatus.php' => 'd08f7b644f4f450860017120a2e20df5d4b2160bd05ad48fc7165dd72976f45a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Enums\\ReservationStatus.php' => '5e0443cf7b4558d16466ca0122b20dcfa9f4b5ee762273dfe565230008994759',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\BookBorrowed.php' => '17bcea9201c6ac5a9ed01e77453b731ef3536850546cd5eb624a73bc7be3577b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\BookCreated.php' => '0e0c82bdf4013f0b21ca1569d5ccfe57447a4fbe864262dc49f186d8221519ce',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\BookReturned.php' => '1291d2689f3634e743237465d0ffc17638c0c044d3b13c37408afae1fe0babdf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\FineGenerated.php' => '05fddf5aefa2c4a277c7e74dd1c39de41fd61eadd2c5b259ed14ce68d36d39ca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\FinePaid.php' => '51fae55f4846354e16b96ed3cbef98d33bb448fc02d17a5b0844ace64a2a8cef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\ReservationCreated.php' => '04dbce4ccd1f28307e9b4b9960aae9aeea83f4fb89292ce15d45ddc3b58be1b5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Events\\ReservationExpired.php' => '299a58b1d90c8467608678931f34b03d0b379795494573d0fc262dd1d8249e94',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogBookBorrowed.php' => '23b8b851cfe6565f99e2cedf6eaf6ac41770604f8b408a6d0bc4df523258ea85',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogBookCreated.php' => '62aa25eb5c300718cfb84a7b2a6ecdf8eadc2ae46e9715d5f98023ee53edc478',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogBookReturned.php' => 'b133cf43d372b458ba8b8ff592a028fb737dc3b5968f6e38904fa996f903287e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogFineGenerated.php' => 'a3c058f2311ad010e8391696073843cd8c504984b2d83897f6b207b3166cee09',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogFinePaid.php' => '20db0737ae3d1607c89db1164d7597e28dd56e1ad94940361417811b93cf94a4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogReservationCreated.php' => 'be246f6c16a1f6aaf1e6b31eaa6b11b4d82486a23c260e6e5bef85edbb5c34f5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Listeners\\LogReservationExpired.php' => 'aa8acb4afe567bb6bc1dc0d5827186233a5d13cd363c772d577a5d6f69794691',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\AuthorPolicy.php' => 'a4a639796790be5c7f89a933dba4b3355d08deefe5497a333c7063ea3402047f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\BookCategoryPolicy.php' => '320f056fca4a18b9f310284d769ba7da4f638f637940c4baf5eb18db755fa105',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\BookCopyPolicy.php' => '9efa5788e0ba6e5f6e4b1e1de06aaefc440c14380aab80ad16f920a55a930432',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\BookLoanPolicy.php' => 'd78b7c6f7459cbe0dff33efc9b6e6a868bea69c2e1f4860c1a4576edf512bca3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\BookPolicy.php' => 'c54ecd6cd7b18bd880a085826e2c1a88a76ffbf30529106095dbae77a853bb36',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\FinePolicy.php' => 'e07ca4c64fd424ae799945d810c7e615a0b6791ef94059e509a199a0c68da04e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\PublisherPolicy.php' => '610e0084cb2d238b2f231da55458c4a0d04011722746fd19c87b189e8173160b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Policies\\ReservationPolicy.php' => 'ec4b6d3a277ecc047087edca49737175b9a7371a9961ffbf9a49863b710d9ee0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\AuthorRules.php' => '0770f28ac0315f5dc911821b57612b82b3e352f5e0f69e407e295582dcf3a4ad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\BookCategoryRules.php' => '528a965f398395dc132d00890c787d56bbb836515ba8217aecdfa1d944188f8f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\BookCopyRules.php' => '46497d4655ad4f363a9f4e5205238192fe4ebd8aa34d798f6a61b6475fe060fa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\BookLoanRules.php' => 'e93afc32a1ff0d5583f8da19bdec904fd54baca87b6e5566c3f2b11ebeaa85ee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\BookRules.php' => '157394f34e63ec07fba03928e8247ab9b552b14cc1fede9aaff135eff7695ef9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\FineRules.php' => '20c867db816168f48c12c64faf11ea34108face4eda801e1c7f127854da475bb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\PublisherRules.php' => '214ca5686f66c21caf8df631f19007ffef45d480ad434f7b96b2864a7c1dd678',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Rules\\ReservationRules.php' => '8592af45828db33d4c7083fa8ee260c98dff2a32583bf324698d9bd7f4c098e9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Services\\CirculationService.php' => '2cdc0015886b7886664a228333e74cd4007bf5300b21691daa10c27d55e677a6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Services\\FineService.php' => '7ea05d297030ae9f8909f60b8d50630bbd72a30fc40c697f265fecdff2dbe963',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Library\\Services\\LibraryService.php' => '1bb26a02737932615100f471a0ad5d561328d5b5e754652dc469cf00971b5129',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Payment\\Enums\\PaymentMethod.php' => 'e020a538013db0bc5ce3045b9a471a13bed85ac8c975b3fcdca4227976caefc2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Payment\\Enums\\PaymentStatus.php' => 'f5f63d039b5468168551bf35967cbfa2b671be0863de08c4ead83532f5b69632',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\DTOs\\PermissionData.php' => 'f59ced7868cc5ad1b357e4e102db87d55ffc2d1e2f9af4fee1f4764c0ad5198e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Enums\\PermissionGroup.php' => '624e0b085e8fd7cf78b853453a86d340d6cfa8f6540ea5c0b081fa2c8cae9cd4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Events\\PermissionArchived.php' => '0beac47945862216bc83d62d6708d8ad73f7698d4961ab47f291449b19ae772b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Events\\PermissionAssignedToRole.php' => 'c9fa9408306dc3c7f2b7dcfb870111dd01536359062051cffbe2265d007c2c0b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Events\\PermissionCreated.php' => '05fa04309e861abfcaca238dd0a8225e8b24aa8b7b33f37ba30bcc16f864b27a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Events\\PermissionRevokedFromRole.php' => '0943fd7b47ec0c2ef8d01cce4e01527b47c90604ccd02109e56a05d48c0200d0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Events\\PermissionUpdated.php' => '2343e54025d845db20ac0eb3b0af027c5158fbb3a05c1703501e854bd495107e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Listeners\\LogPermissionArchived.php' => '4dfd60c7fd858da1b20a48b3726650c31cc1dfb30afb3b478e26f64303f23f22',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Listeners\\LogPermissionAssignedToRole.php' => '0c4bdb06ad09ade531e3a444afaf796799ee8416d9aa3787b758fbe1115bb7ea',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Listeners\\LogPermissionCreated.php' => '945894f43a245b9279d7e4e99bcfa813a808dd043bc642aaee9922da21813f9c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Listeners\\LogPermissionRevokedFromRole.php' => '9798590fe6231f5da4bae3da4339d33544f2c779880546476a00747fd3eb2ea2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Listeners\\LogPermissionUpdated.php' => '28af579ad3f91a44029a0a6b6a178fa672c2d0f3fce7c8f072b16755435ffd87',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Policies\\PermissionPolicy.php' => '74d42c0f3706cdcb48ee19d7e319ceed454e90e079b4da9e426c44c06a677608',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Rules\\PermissionRules.php' => '0df8d464d56f0c89330edab355bc51b14f2d933a3c331d6a31e91d9cf4e5743b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Permission\\Services\\PermissionService.php' => 'e988989b38a19158c3308d65782688f6a572d0a3c0adb4d72229b98d2b6f1b52',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Contracts\\PluginDiscoveryInterface.php' => '282c27ed9614146d648c994207edf80f1d1e28368fe32dd54b726c5eb7297d54',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Contracts\\PluginInterface.php' => 'a8f4023a3bf6b0994e7ee1982f512dd8e24d6af68e6538c040b8ed95733558d6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Contracts\\PluginManagerInterface.php' => 'a84b69a5a0e47168388730bc8d63a019ea2431eb7660891b006103db0c51ef2d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Contracts\\PluginRegistryInterface.php' => '85432c27397408f017d1675628659de15ed259014a8141bf65f65c4dca14a454',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\DTOs\\PluginDependencyData.php' => '47693fa34ce6044fabc1eb3d283f7d0a462f8f3bd4ac48aa7bd847707aa6e663',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\DTOs\\PluginFilamentConfig.php' => 'ebd2c8e0c439b9091322085c3ec8952b23a055dec6b3bce40bbbbe2323c902b1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\DTOs\\PluginManifest.php' => '921ecd76890ad6eba89adf35fb1ac314abea6d59e389703cc63d44f1700aaf61',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\DTOs\\PluginMenuData.php' => 'dcaf063d90de41f67e52a7428b6d9e8bbcd41ed4550f1f493531b4b96eab1ca5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\DTOs\\PluginSettingData.php' => '74bf53d90b8f970b7e29942ce6fefe180b1e6b094d5ab11215b924b4294cde5b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Enums\\PluginLifecycleEvent.php' => 'b9d4b3328a980148ba88b4ac637312e1ffdc15bd92efd0504be5ae2fbe0763e9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Enums\\PluginStatus.php' => '2bd463bad62fbacb7ddbfec191fa205511190db17a01928cba212932ca9fe2f5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Events\\PluginDisabled.php' => '9c3a11d0a452e8525cc2fc134fe6090bc88f6f4dfe737851c8caaeb1547e4809',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Events\\PluginEnabled.php' => '8fb262e9813b93570768fa368b645cc66cc21aba9ebb8950a59ea33c5f189eee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Events\\PluginInstalled.php' => '37b93fcc43af2643701dc1f143c3efe1ececef4286d8eed4614a2a3dea4b9f7b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Events\\PluginRolledBack.php' => 'd7560b1216bcc7aea49e4b69e33f4d2038049c98e345ca263f1e2f69c5362a14',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Events\\PluginUninstalled.php' => '32764f36f5658953e631abd4c533e4d5b4943597634a74997da2ad235676bf3a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Events\\PluginUpgraded.php' => 'ccd8e2194543777cdbf95748308c47228de4ae75963f602af7f66adc3b05b9dc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Listeners\\PluginAuditLogger.php' => '8cb1bad44184d612526c786cf6a7d43194273fc1bc02d8d3b4bbb38ce2fd4893',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\PluginServiceProvider.php' => 'e6c6bc93b4edf9211c352e266b3a4dd38273ea0e685ec185eecbe5f1951b28a8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Rules\\PluginDependencySatisfied.php' => '07c10ccf9e9937cc9d9ffd1eaf500205fa65db06bc1166b9dae294a9259ef25b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Rules\\ValidPluginManifest.php' => 'ce138cfaaabe891d128138fa48f0fd3187ccbd453906d2449dd2d6cd845826c8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\FilamentPluginIntegration.php' => 'f54d5731c71336b7c32dc961d4936247b56eec79fbed1d2cb1796bbea9763105',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginAssetLoader.php' => 'b186f0e6527fd8cb7cf2bc030f655121aa9f78cc49c8a1ad7730265db59a3ca4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginConfigurationService.php' => '1eda2cd6ba46908b47c61999b5e4ce720a86a8ef71c816ef93463e655edc2394',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginDiscoveryEngine.php' => '2a9168c454840b260744ac8a3d31acf98feccda5e004401eaa5b5a078b752e4c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginEventRegistration.php' => 'f040a74f6a05bf797e26c32f196959f19dcb5dd6bad7a05d79b2b90a8c3c2702',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginInstaller.php' => '772386e110d85da3c6cb761f9c0040dab1531a90cb5ab096a67f4ef2448e4962',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginManager.php' => 'e8a5428cc7f112a24542726477a760040be7dfc35d08b08508320abd5fd82d00',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginMenuRegistration.php' => 'eda99ea2ac128a23a382eae7e6f39d3993a4720d51eb27ef0c2f0996cd9e31a3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginMigrationLoader.php' => '3aa975f77f04f742ee36fdd83f3fe3f91cb270a86389dbe0dc65e46404a4ad60',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginPermissionRegistration.php' => 'e21674b20d15441ebd82073bc2ca80dc19a17470533337358ad44f5639958c45',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginRegistry.php' => '58c7436fcc4249398fcd607ab42437494686800d3982d64bba19f51fd5619ba9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginRouteRegistration.php' => 'adf9aa4dc14a5957cccf9f4af616d718239164e783e11203cb30f7d631f71092',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginSeederLoader.php' => '95c8db9612e597ec9e7b6539b9752c5b67d2ee4e96b33139c67c0b94cd42d47c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginSettingsRegistration.php' => '18a0bd5ca902768b45a11c2d5a0fae62450aa648987749c4b3ffadfd3e48a7e4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginTranslationLoader.php' => '2c456d0e6cd33517e5d6bdcea4e6b34c68970be7d9fdd2e17a4108a331208f76',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginUninstaller.php' => '64d22b77f958cc72a5610a5f53f8f27de706edaa450b51533ebd73b3966bb0c1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Plugin\\Services\\PluginUpdater.php' => 'b00cbf51a7b1dd59c159a368d181990cc651f531d1b101387ff941b3c283cb7e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\DTOs\\ProgramData.php' => '3ca7c6b96e150f90a56f359c140aab908c5d80b5362f27e348913eaa909357ec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Enums\\EducationalLevel.php' => '0236441a6b5729e4571ecdaa2164752d0e4bb6f34e535febee75a4bf0376c506',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Enums\\ProgramStatus.php' => '5fef6cd2ccee11b17d56c5e6041452aefab1b88b7036f68b58f4222c836493b9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Events\\ProgramArchived.php' => '08c161573929f331b453663722bdd42ff9930c1d4c8722e565f385eb51d94234',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Events\\ProgramCreated.php' => 'aff4a854ad0100f2f55024a351a56c4a1af3a1403ce0fe2ded7dd39bb7c718d1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Events\\ProgramUpdated.php' => '29f9154db7b2901fa86ae8bd104fff2e3461f48535e18c8adb52b02114b3171f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Listeners\\LogProgramArchived.php' => '6d71cf04a572fe1d7f4a92d02816cac9333eb9990cccffd9d20b6394facf8da7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Listeners\\LogProgramCreated.php' => '25ff9ca8735a5eb7f3aca20a75548bcd3b228a533ae7e104e4d21c6e4108dbef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Listeners\\LogProgramUpdated.php' => 'c428d7295620f4947fac2cbedd443842990aad12499941d211119a52a0ac1274',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Policies\\ProgramPolicy.php' => '32a78af3a6c2e1f621667ff4381dfbc183d930de8a9a9a5530bd7b33a9def345',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Rules\\ProgramRules.php' => 'c47fe1fa2f188744975cf29d9c852a3670383fb32d9da341f2fa3ffffafad7ec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Program\\Services\\ProgramService.php' => 'b827ebb1b325ad4f93d93fc5342f7743db55cd87039347fe549379d9476edb89',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\DTOs\\RoleData.php' => 'f22bb5a7d9892517d8a1565f7b4fd87babe83aa0c0bae3deef81e83ad4487564',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Events\\RoleArchived.php' => '8f72d88cf158c595f1b6103407c05afca6c4005a5931656f2b74c0eabc2dfb19',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Events\\RoleAssignedToUser.php' => '55d01f9ffba4a10e8f25218a6bc688d3e58a126c48a16242dc37fe73c0e1f28d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Events\\RoleCreated.php' => 'd27278a5a9df9c691df1d00e5c0ac88d1af3d36a61c1cae038fa83edea6927cf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Events\\RoleRevokedFromUser.php' => 'da3bceb710fee203802ebd45e70182c16d2abf00bf4bf66bdb3c6c2237c5b55c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Events\\RoleUpdated.php' => '9d8dc04f297bd8a6d2069949cc43e6cb31d29979c990073e09eb6f7411e39f0b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Policies\\RolePolicy.php' => '7410707982de9c46384d82e09adbeeee2343eda48ae70677726b29a1b2040e96',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Rules\\RoleRules.php' => '18077e94d5458b31bd221ab2c9603ce438cc2c9fefb3d1dfb0a80bde12578cd5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Role\\Services\\RoleService.php' => 'dad3fbeb8765017d47c29bd4786bbf5f194e569792ec039c32b1088a2cb93255',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\DTOs\\SchoolData.php' => 'd5ccacbe09d3a68126e26877c4176682ef574a02231151908b9fbcf558129bef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Enums\\SchoolStatus.php' => '9669d653dc576c26a5afdf874996363bb2cc0360f523661521b7b23a44eb7d7f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Enums\\SchoolType.php' => '779e5c2d09e407476d6fa631df28cf824b9126387ca67672aa4e9f08d1a1a080',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Events\\SchoolCreated.php' => 'a3d8daa64f88b13f8c40625c4bca15fd49404dec856fc886d4cd084a3d3bc100',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Events\\SchoolSuspended.php' => 'e2aa012bb3fb91878715ba4a668a12663f9a8e7f0033cb85b86fec7e3162726d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Events\\SchoolUpdated.php' => 'd9a2405aa7cdf21876c8b1c3c5484cc526bd432c1fb334391df28184060b2c53',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Exceptions\\SchoolNotFoundException.php' => '86664f24e749644eedd3f577fb53d22ddff9a501a23d2cc454649f5a8b932e84',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Models\\School.php' => 'bdcad6bee4d8a04bf6a0b838e7a74efbd2fb19f6b45f79ede37ead409f9aa463',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Policies\\SchoolPolicy.php' => '18e45242376df04ff9019cc8a3f66d0099fd3f239f1abc7b825b66d8316e625e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Rules\\SchoolRules.php' => '852b4e61ca1509adc19b97a7af48279f8f0a9a4883ef64f89b783ebc2ddb3d1a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\School\\Services\\SchoolService.php' => '0a3eff73c9ddb7ec2f0ce7e4d91e4c4fa41615c46ac12db9796e39b76727b2c7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\DTOs\\SectionData.php' => 'd425949f268d86a7c75238b2d2ae0b4f76d6f7363f5bd8d3c2ff16d258fdade3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Enums\\SectionStatus.php' => '159073f00209af082ad5d822154ec0b318b14fe753835d058558c033d5763bf6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Events\\SectionArchived.php' => '73e24d86f28986dd8aef9b4cf89a6b433cffd6b0394ab6a6cdda333bfa068cfc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Events\\SectionCreated.php' => 'a2378511f511acc9779cd9f557a7c8ea61190d6a3915f53cf065cf2cc338ee77',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Events\\SectionUpdated.php' => '1e0ada81dc2a497a43fce110c7f4ec740061eb42f4b7f697f544ff928d38497e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Listeners\\LogSectionArchived.php' => '68ba0b71beefb60b61f562adcaffb6d4c46a047bd9244c3f5358358506ff028f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Listeners\\LogSectionCreated.php' => 'e3eaa3bb3b2a97385b49f1cdfa99d9cae78dc0b0a9f327165067d13c94c942f3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Listeners\\LogSectionUpdated.php' => 'a07ab29382031e05d0ec8955262a6654e6bb829ed71c103f15e5d5a8112e490f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Policies\\SectionPolicy.php' => 'b782b6a414605823d5278d28872234c22529cac997ec4bc5612135448a3855f5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Rules\\SectionRules.php' => '7f8a0d76051433f4514af901a176b354476e163e13e993c858e682fd989e465c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Section\\Services\\SectionService.php' => '0fea18e8f09e51f47c1bf4f7596066eb57469a9132dc9a13c108f33a9c6221fc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Shared\\Enums\\ActiveArchivedStatus.php' => '29c3c1f2921d58ea6c054ea52de5f733f42c242bfaf98cf7eb2b307540c1b899',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Shared\\Enums\\DraftActiveArchivedStatus.php' => 'cc28c629481569f6fb6a4f8539759ab9480171a1a4561c4884216804795e62d6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Shared\\Traits\\HasEntityExtraction.php' => '2adf2fa1cd18a888a5c92b964900b344ecd9147637a852beab31be6805d60e57',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Shared\\Traits\\UniqueValidator.php' => '32b76ca7c12112d34c506ff2bc1f9718acfd239fc32cc617429a33192f3f7a79',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\DTOs\\StudentEnrollmentData.php' => 'ef9b36715bf1ed429efa623b2ad52a22c417163e4b2e520444322140ecc97ea0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Enums\\EnrollmentStatus.php' => 'a2a09ab438bc14af52a46427cc4c6b344897398dcaba01dd16d2dd68844215e4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Enums\\EnrollmentType.php' => 'dbcdc38a39769181ba7db6838765d4deadece34f6906ed12549fdfb45f88d411',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Events\\EnrollmentActivated.php' => '97c1e3cec1309507ac8c3c1912ecc16de84ad9e98a793b66bc90ad91c76420d4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Events\\EnrollmentCompleted.php' => '4d37b6c4534580b9502bd23ba498dca3a6468ac472e80ef6638f5fc013a69071',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Events\\EnrollmentSuspended.php' => '4872b850b43d833ef93e5170f79415e5436d75bda4a1941828d811a3f626cabb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Events\\EnrollmentWithdrawn.php' => '2a0504989cdb3297059b965299507e5544d1ac76753843d3201cddeaf70fbcfe',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Events\\StudentEnrolled.php' => '91eb79c66623660bd4ca68da87722a347f054c215aa51e6c278471e4348a68ee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Listeners\\LogEnrollmentActivated.php' => '34db99b6d5670ebd14cafb568f4769ab8bb71b2a47ee8e9994c62872d409308f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Listeners\\LogEnrollmentCompleted.php' => 'd48c3cf438c87fce124503c4615cdd6579be73e3b911a2e3d250f5aacb276645',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Listeners\\LogEnrollmentSuspended.php' => '503f0274f36b195d301f17d3bbf03bf4b4881160b23553045eca3b028d616884',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Listeners\\LogEnrollmentWithdrawn.php' => '9e01ee72cf6c2df3ae43b608e320f6858e2e5a2097e80c76c1cb07becccf357a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Listeners\\LogStudentEnrolled.php' => 'c9ad20b95c912187279e3839ae71375e48c501e6c5a1e4a374c4f5e945213c80',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Policies\\StudentEnrollmentPolicy.php' => 'f2e0da52d993fe5b36ca5a5ad3f2edf4003973d90f9ed610ee337cbd7f7d98ad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Rules\\StudentEnrollmentRules.php' => '0ae68068a298f856437f7a1a61ccb06d57a3cd7ec19904795d3cf04d509f08ef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\StudentEnrollment\\Services\\StudentEnrollmentService.php' => '274f617a093ef9be6cd25dbb87424a58b791ec889ed688a9f760393e9d815048',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\DTOs\\StudentData.php' => '4a99f85bbfb9f886774ba5e879607b881ddd62c3108d31aaac6cde6665e2f86e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Enums\\StudentStatus.php' => 'ef921e441c9defd8363470e03e22f856e09a44d14004f97c227f3ba32e212a51',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Events\\StudentActivated.php' => '00e709aed07b8bd2a98abae9fb4c45d96d0f43841a99ca48c152b199e83cf770',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Events\\StudentArchived.php' => 'b26d9c9de672dc0ce46ed65724c604c3ffcab73f815eb09353c26d61f4f1280b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Events\\StudentCreated.php' => '37a54de375762fcd3d4d664d37b251b7cf571b220cba7deeadb58680b70092e4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Events\\StudentUpdated.php' => '0b4c99afdf81225f7480dd17230762398fb28c6ea9a6acffb580ec9d68461c10',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Listeners\\LogStudentActivated.php' => '792caef280f04de22e324bbb973568f40e4ccfd190970c2ce796bcc4f159de98',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Listeners\\LogStudentArchived.php' => 'd57060b8d45aba22687fdc5a6adbd0effabefd6cf81dfdfb62f4cfc31415d472',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Listeners\\LogStudentCreated.php' => '2a51b1fb4249ace563bdf064c4a5c8006529987078a0cb9d74405eaa1090209e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Listeners\\LogStudentUpdated.php' => '3aba2df0daa558a98ad318d91528fe10e29fe64adb07e56bd6ef526d622c499b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Policies\\StudentPolicy.php' => '4378b3f4c570702ea16fbead875fcd89245ad5cab22b0fa12d6a9788054b5f8b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Rules\\StudentRules.php' => 'a5123546017a589e361b0760472d4ad4a30e9c2e233018c299b89f95d51d3a35',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Services\\StudentService.php' => '294b9acfacb02c3675c2e9e6e7f0024db6773f97647cddb8c888d2a8eb09006a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\DTOs\\SubjectCategoryData.php' => '3d7df872786ca9b20871f32516f799ad9494ae5035e91e6bc9fe013540948d7c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Enums\\SubjectCategoryStatus.php' => '628d08bb06feccdbf41d067130f07b25cfe2247f46e00c7483645544c4dbea56',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Events\\SubjectCategoryArchived.php' => 'bbd5d982e458aebb49e0ebe317f9bd21aead2d139d9ac1655d9bb837e1e04e98',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Events\\SubjectCategoryCreated.php' => '6d0ae1363b9bbc198c341b0d0da87e1aea2118b0200c345dd59fb88f27793a4e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Events\\SubjectCategoryUpdated.php' => '53f644adc31018347e5cd5505cb7bd0fb341f1994341678c07c189dfa9a251b6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Listeners\\LogSubjectCategoryArchived.php' => 'f275130cc03d9336603d2582e98e9e2fb24aa898116f630decd708724d9bc0ef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Listeners\\LogSubjectCategoryCreated.php' => '620ebef5d90291b3c3c30cedea705ec67202de71e591525518ce49b0c78d9b47',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Listeners\\LogSubjectCategoryUpdated.php' => 'b986c6ed6f905208910cb1780221ff6ee3653c7d363ca6280ad2ff9f8e730771',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Policies\\SubjectCategoryPolicy.php' => '439a5d501a7030ea4c6c6fcf38368aa5b4c8f2ed75b3843d1eb6e1f028a347cc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Rules\\SubjectCategoryRules.php' => 'e25ed7b60ab54e5c72f63ca681789ffc74585a27fdee169a8ed1a373d19ee229',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCategory\\Services\\SubjectCategoryService.php' => '52928a03f977e1b07a3fb9da7b5c28e3c40abd4df1302cf9aa0151a252e2a881',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\DTOs\\SubjectCoefficientData.php' => '2f0f77400aab7ea3189b1c3df7526b1a7a4855e6c3d507039d3bca8c33dd7c23',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Enums\\SubjectCoefficientStatus.php' => 'd92e6203d8a213c595fd2b65be54515c00b170ddc9cee0e59e3333b8e7e32044',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Events\\SubjectCoefficientActivated.php' => '1da687a10246a802013ba24ffb17c4b459e1badc7513b21eee2b97eef13591a4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Events\\SubjectCoefficientArchived.php' => '7bd76670325d4f4871e54ab7179b978b25ccee841c13c39ba762f7560ea2746a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Events\\SubjectCoefficientCreated.php' => '6e471a5ddbab009e23f7d24108906a3a3828af2fcf85c2f433aadc28d49f1a63',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Events\\SubjectCoefficientUpdated.php' => '143a754c2f259e5b6ba7ee4d83d782794130baa3136cfa435438e6c7d94f34cd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Listeners\\LogSubjectCoefficientActivated.php' => 'ec844529c99ffdcc54971b6fe185dc40952b785f6c206f59122724cfac816dc7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Listeners\\LogSubjectCoefficientArchived.php' => '3fccda90ff3c5b9403be5d4c6876ba8b2c49583135b6d73eb2f3e9e2bac8eb46',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Listeners\\LogSubjectCoefficientCreated.php' => '63327aab1c6027d9452962ea89482f59280553d279fe377903828ce365c7f54f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Listeners\\LogSubjectCoefficientUpdated.php' => 'a06669afb593c3d07a45403d52cb0cd33d2ea3b5e25363581150240d6e78b98b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Policies\\SubjectCoefficientPolicy.php' => '01951fa388e4b9f5f22876dac4afddd8eb3ab95f83286190dec818887ee8da11',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Rules\\SubjectCoefficientRules.php' => '734f569601249be2d6a06c979ac686048f60c218185c2cfed837172dac4fd146',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\SubjectCoefficient\\Services\\SubjectCoefficientService.php' => '01d525d7aaad010883e75dbeeef15aeb4f05867626d5b2cfc8b0d909e0aefca7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\DTOs\\SubjectData.php' => '8a7a13cc95cea8728d8de3b7840bd897786a429ce468510c06912bb1a38240a0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Enums\\SubjectStatus.php' => '3886e784bcb9fcaba780912e8da4e8487017771fc26814ad6f48995de817acc7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Events\\SubjectActivated.php' => '0314e769d119051a369a32b69999f8c3680fbae84deb2815fc937b74015cca30',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Events\\SubjectArchived.php' => '455dae72278bf2c864d7460e930b1f63419a186a7458013f58ab3322bd812ce7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Events\\SubjectCreated.php' => 'cd9539b91aeb526ce5825f6fd6a429df0550c06e0973be09afde05c450df2f6a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Events\\SubjectUpdated.php' => '56085cca4ab11a4a156be58e9ce01893d6d9c7811c29e917fa89218ce89db6e8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Listeners\\LogSubjectActivated.php' => '8a04dd38cfd29f612e3396c8812da4ec3003c80d03137975c7db9bf16bc46df5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Listeners\\LogSubjectArchived.php' => '02ca5f57d272264b561936faf76f9777706723fd57a5f9b6984931ac313a2c21',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Listeners\\LogSubjectCreated.php' => 'fe245a264db37cde19269c728587bff89d50d9d4edf7c67d6fc3a1d7f86244e3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Listeners\\LogSubjectUpdated.php' => '57cd3ecece09e2f1a0023cab929dabc0a941bf04cdf01b0a797ee7597677e56c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Policies\\SubjectPolicy.php' => 'eeeb7d3b7d42de7a71341b1569166eed0323aeaad029d4ba628e29c275a817cc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Rules\\SubjectRules.php' => 'dc03da03baf0ff7c5d898e793407e90d927549a679711e638e819404bb774728',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Subject\\Services\\SubjectService.php' => 'a7195cc85d4ef579f3ea4b4589eff4d392a2f1fb276cf9bf41fed43a1f744dbc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\DTOs\\AuditLogData.php' => '8050e22ed3e97372d15af946f346577660d8c282e5978a946d8d6cf3e5cc08e4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\DTOs\\BackupData.php' => 'c2a9cbdba2dddb0e02f097267e7c1e30c5b2bd0bda895bb8a20d23a09ecf0785',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\DTOs\\MaintenanceWindowData.php' => '0dc037a4d1f63cf8cda08d6560f43f6f4a833875f0292a342f6334cd459f06c4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\DTOs\\SystemHealthData.php' => 'b74ed925ca12b1b78380e1622bdfabf804072ee3da96568df38c04482246773f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\DTOs\\SystemSettingData.php' => '0ff8b974fb1c4304032b6773d2908bf9b17898060331d2439f1cf2bb56d2646b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\ActivityType.php' => '15d8b85606a0354962e8e57ca884638ae49d1d66bcd62d06bbd4a45ab2e69f76',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\AuditAction.php' => '86100be3a48b85517dffb7b4296dfdfcb2af989069b88da5489dab43129ee6d0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\BackupStatus.php' => '15612d025034cadd779c1fd4593f02b4a0a1ba3224614b14d883b1b272697d55',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\BackupType.php' => '229ca7b718cf7d521e75924804a75add6d4ccd926e827e3a640efbeb2d251086',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\HealthStatus.php' => 'bd52621f0830bc046dbc3d19b3bc7011d4a14c22460e971e57eaf7823d9f8b09',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\MaintenanceType.php' => '4c6bfdb1ccf21e93f82058826b0b48c1a50cd56090575cd0799578f06f140d39',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Enums\\SettingValueType.php' => '965a4fa07cb7c03df1488558770e431cc9311c1ca9cc4a70a8c3dc3fbafbb389',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\AuditRecorded.php' => '8d19f68cfaed66762f7a255143c9da7cce1af92e405ac42fb2682fe8582b0767',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\BackupCreated.php' => 'c0a82945047050898b870eeb62a383788fd535a2e20aa2999605b38d56660c3f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\BackupRestored.php' => '58d6325e2c093a8070e415b5194241dfb8bcc95d8a216a6de26e10ad9a8d4e4c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\HealthCheckFailed.php' => 'be850ee9c7ca8f71f1115ac3b190c24c5e175a7f42b90a775e9c21754b4c6606',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\MaintenanceEnded.php' => '15789e0ad1cbfa3178ac319cf71932408db51d9f9ddb9ad63d3d9284b1967b6e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\MaintenanceStarted.php' => '3df258962ac324cac84558d644869a5d142fee2b5764dcad72b9009b0dc75bda',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Events\\SystemSettingUpdated.php' => '05b177042dc0d9703b53e53366bd4156cb8a1711eb55012964022dfbbf18972b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Listeners\\AnalyticsCollector.php' => '6b47b2b9139e0529ce498d1fce2d9873fbb9f3e539d854d49f5068842ef1e8d1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Listeners\\AuditLogger.php' => 'e2e8329d412fa8be1a7e4df529f8ae0e50eff566f5a761745de3289644c2e3e8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Listeners\\CacheRefresher.php' => '07ec8ec74fc09366b01ec51845370510507be0f9c9bddc249573b1e9b0886882',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Listeners\\HealthMonitor.php' => 'c05ae6cbd273dffbef515eea109fe983ad1066496a99c85bf114301b94b94fdd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Listeners\\NotificationDispatcher.php' => '6ed1fd9faef48066471227744d640234d5b129a54fe835b50613fac9403ff04f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Policies\\AuditLogPolicy.php' => '0050a99d241beb3c241b8cfc8c1de61b52fbc6e2cb3f7e903ff3cc8ef21b9c1e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Policies\\BackupPolicy.php' => '94517ba4d6875267532f32a0ce0306aedfcfc5f6d44e7beae4ddbd60cb3699ff',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Policies\\MaintenanceWindowPolicy.php' => 'fc3e0379c9118b0e5d0fb7ef39bfea8fae47db97c66856ba977c10b65f443d7a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Policies\\NotificationChannelPolicy.php' => '91db1bfb59b50b7289c8566c610d26c4518560824f3361a8ab326a576de004d7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Policies\\SystemSettingPolicy.php' => '35d16aae9af93ea39c8c506d3048d81ce90b7aa3a59b95edf7e6a802f11a9940',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Rules\\BackupRules.php' => 'be0f50db683d538870863df513d980104d948efc866124660a8feab4d1ec8f9a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Rules\\MaintenanceWindowRules.php' => '52376de6207d1cecca864c992219392380a5a7d11812e1dff79ba6439b775e94',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Rules\\SystemSettingRules.php' => 'd63b23413aa16904a8b51464e62b3abc385b050c8dd47bcbc7b5b3d1c276b238',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\ActivityService.php' => 'a8535cf7fe8871ef8bef977d22593f208afc58207fa1dfae74162325fefec03a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\AuditService.php' => '6ab986b2fd33c9fbd9ade64cfe82236f2fab2d68763fe24ad859e93030fdf620',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\BackupService.php' => '9d2683b5c1ec5d32ce2e8f3458443ac5ed930583d90437c3b32993b9f72d0079',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\HealthCheckService.php' => '74615a0a3f51bb3ae49c93afa4869ff124985b45f4aa478f96cda9f75a15f842',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\MaintenanceService.php' => '1a963e7ba4ce602697780d71488cc104a98912bb2392f5b59e2ae192ed7abdd9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\MonitoringService.php' => 'c5a9e569fcb882e3bca32bcd50ca7dc07b01611b6e32f281754679eea54c393a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\System\\Services\\SystemAdministrationService.php' => '27bcd3315896d307e5fc588893a0dee206670f2415b879cdd6d34c7f669e8f7b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\DriverData.php' => 'e9696d894fbac4e1c4e29fdfd36dd9b8432d911041a375db4a68cdf5e0a2c26c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\FuelLogData.php' => 'bc681020ba1a7da4e90eb53d554ba7adc9314498d4716f791997e0a07883653b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\RouteData.php' => '5cfa3f8114cd884075192b2a6ea5cd4a7d87c7e2254f9752104aadff52deb1d3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\RouteStopData.php' => '9239a240b6f08c4186d4d3ad92e099f28e0f3f36e3651c42dc7d3347401ea4bf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\StudentTransportAssignmentData.php' => 'eefe353bd9dbc4e056ca09a2f8597f9ad4649ad0842ed467aee0b27f2577816c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\TripData.php' => 'a816080751963f0abecca6a1c1b51a537b20ad0b9bd19d4a16f57b279feb3dca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\VehicleData.php' => '6771235b193b3021d9eac23c5208d8333ad9d3b43210b3b48b7179be8d1f0e1b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\DTOs\\VehicleMaintenanceData.php' => 'edc8113bfe61bab7fd0c0e317925dcd3bb6e861ccc7e262a8705e2951835b345',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\AssignmentStatus.php' => '25c8b2feac135dcbda16838a532d31c1bc023b7ee86cbe69865a887e01a093fd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\DriverStatus.php' => '924e8875b7adf721f97a01a52c4a609d26c450cbb2649baa1bb6176fe9f3bfa7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\MaintenanceStatus.php' => '3fd390b80905d07ba0de61934b357e65179ade31376f112fc41f1dedaf9584f7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\MaintenanceType.php' => 'edd9bc10cc303f479e9d381aecaf65c44c4b7f7c80a3838c5af38da3192626ec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\RouteStatus.php' => '546c3d3423c54d3ed06f4d445f2a1b4fddc48f2521fd9c78d998e3acdd78ca8a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\TripStatus.php' => 'b5b28bb5d5cc0bcfe59d54384f55534bc8c96bc1fbd5d4be1e0e3c8d77f14bd4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Enums\\VehicleStatus.php' => 'e862bae18abc0b3091ac43cf2dd8646970a5dbdd8cca9884edef3b8c66d0f1f6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\DriverAssigned.php' => '0dd3c454f29e8a66beac4df401f3268a549b2786c67ab6315aab4b3e3c916fc8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\DriverUpdated.php' => 'c070c3326a7fce61b544c4b311e79e418621e68a8d009b77df1d82664aa9161f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\FuelLogged.php' => '6ae10fe3a2bdff15fe7d8e57382d62796126cb434f3e6b14fa2f94fdd80c8471',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\MaintenanceCompleted.php' => 'ab7b7d02ceb8bf2239ea062892d958b3f60ba15b8b9758ed8c72e89a54760471',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\MaintenanceScheduled.php' => 'c89374e75be0ec9b60ae257a38e6c1535538c3f2ba36e10807b06236e960c878',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\RouteArchived.php' => '01bc52a34e149c3965e6cf957ee91e15caf561edd5e894f2011d4ee53ab51a3c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\RouteCreated.php' => '3c28fd1c0cf05cd7605a7ba0150189b9e17b665d662a38e8553d18214688712c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\RouteUpdated.php' => '0c2a9a9c47647340a423af6502abfc22dd507d15bfdd4784341823060b7db5b1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\StopAdded.php' => 'e74ffa9fbfcfed66ba8fcbe891ca5028f9698c151733d67c403bef8e6a31cbad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\StopRemoved.php' => '219aa3ab455913b1f6db27b25c366378b752f3437a8a2ff13239cdeb4074e15b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\StudentAssigned.php' => '03a271b9dd14b560124d5ca09c6e1827b625ef83113723c2530465ceb63cb346',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\StudentUnassigned.php' => 'f184ae4468b5b7cc0d2ff9fa8985f589615a1e8fcbdad3db240d228412deefc2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\TripCancelled.php' => '638fbe8742f784ae667586fef507745abfff6092fdf925b59894e52b8a1fce6a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\TripCompleted.php' => '115811021f11cf8bbf0d586f4df3c98fb495fe0e339feeb957f7dd64d24c2011',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\TripScheduled.php' => '4847068b17d271b001041b543f7569bcfcc12db4a9fb4f24937d81ea5326da79',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\TripStarted.php' => 'c4262c0766a585e9eb1577242ab06bab557ff0de3aeb18549a621ef3644b8d37',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\VehicleRegistered.php' => 'cb728be83f75b8aca0925e4737a268683a7bb8fbb998ed5b08217148bfc9f442',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Events\\VehicleUpdated.php' => '3be18d6835cfbfb0f81bbf999a3658d9d97503d4ddfdfebe5cfe0cbff3e46a6a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogDriverAssigned.php' => '3c2f6a2353faf6418b872c99a6da7fa5660708911e39443a23f792d499f11667',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogDriverUpdated.php' => 'e73beb5f8a578392cbe286179a8dca10c723f01dac5ef3416060f6f911fa06c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogFuelLogged.php' => '14425ccb7c43842785473fbcd3eef743d09f7cdfa0bfce6d9c8f3a58e6cdbf4e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogMaintenanceCompleted.php' => 'bf2d51a9d640aabf005e18ec44a5125e811b2c1f57dd7f1d7ec499cf8f062444',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogMaintenanceScheduled.php' => '914b6fef9e5f48528d767c653311361fdb35d36819f79626e2abcbb8ccce8494',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogRouteArchived.php' => '2f9ad61f785bcc360c5dd4408e5b238c2a5b516d36e0643a9eeb6d63d6880713',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogRouteCreated.php' => 'd51e280ee32afefbc75dc8aa8bd462659fd8a776039530bb043e6d03000f1fb4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogRouteUpdated.php' => '1af57280476f78332321c1fea5cd2fc48c46d32fac091b4b6350840ee9a1f6ce',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogStopAdded.php' => '1883b4f887a60756dc7f7c42e2b53e30040a611993dd375b6f5c6c15fcfee834',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogStopRemoved.php' => '4844e79cc3fbe57735cceb887bd5ca954b0b84f840c7747168752ab0193024bf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogStudentAssigned.php' => 'c56ecc90dae64e6952744287c40b298eba0c3a984381743dad4c742e41876e2c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogStudentUnassigned.php' => '45aabc9a8112b9b3a528bcfca7ebd685c8848b291c97078ba1b0692f41a2e354',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogTripCancelled.php' => '83f13c8211da356980e7bf64904f7d394d50e8b2092485e6a6402da595ccbede',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogTripCompleted.php' => '00424af42d07ba3d0b87c7156d1351d27d8e7feb70dfb5370a286f8804da65eb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogTripScheduled.php' => '4624a46af6df211ff33f74eb05579f61de364c7064df0bb8008cedd3d30ffb09',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogTripStarted.php' => 'c6f63be1f5639acc197fe8d2c7ebd10ae964572b8fc95ddadef0d3a51ec2fc7e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogVehicleRegistered.php' => '0ee4355547e72405b73f41d29fda73bc7e1a188e85123b3887855a9cf60e8c53',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Listeners\\LogVehicleUpdated.php' => 'bebfc4a574249c1cebe43447aec5a0f265ba1fb025bda3d96ff8ea17e62ae330',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\DriverPolicy.php' => '1f98352955aa102ded738701ec0b4952c33cd89aca352550216eedccc327265e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\FuelLogPolicy.php' => 'ba794860a2c9f1f70b9c2e7c4efe9848c57bc3d138536c6c49504a83f5539591',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\RoutePolicy.php' => 'eea85abeb8dec0476ecfb09d593e16e2c20688ecc38fce1d8eb08f2e6d4198dc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\RouteStopPolicy.php' => '4426bf38383c3275271c021034cff6c97108cdd1589eceebed171cd17470fb2e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\StudentTransportAssignmentPolicy.php' => 'a214ee1f24fd6970d2cf58b9231b04d30555a6525913123c705c21a0d1916bfe',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\TripPolicy.php' => 'b9b3d1dd1a81b636f38275e57b1eb62a045d86c0ab98b0f59b47657d82ed3a99',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\VehicleMaintenancePolicy.php' => 'cbdf022159bb6b860e4097141b108897b7d3e771c8a4c29c5e4bdbc2272a6037',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Policies\\VehiclePolicy.php' => 'b303089a2037eace0df8798b1c587aaccb07124f6cec565739681432a7087d8e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\DriverRules.php' => '95bf49ad6791fa0b408de87ead21e324dc4148ed1a67b7c43b303920bfda63d4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\FuelLogRules.php' => '01a76a869350b730db2b92c22aa7b4e7aa4c039cf8f28c08603ef2dff165c609',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\RouteRules.php' => '0c370c7d373b064e1af68184fa68b2a9b0bb7af9c6bda7db5f9efb72168c94ed',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\RouteStopRules.php' => 'f7bc5c2c0f6debabdc8955715c83b4a391a4fe62520da73b25a2ed8d194d5d45',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\StudentTransportAssignmentRules.php' => 'bd70541bc03b06df0f3a00eb242c0cf59f286aad04b43c964def02a9c2637b68',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\TripRules.php' => '347e1bf290f233d5c7f5b33d331c6c4277310d52a796f06d43cef0084f4af6b8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\VehicleMaintenanceRules.php' => '44d863ce628b45eceff54a16306382d70eed59229e1c3b40bf3bbe6a94d265f2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Rules\\VehicleRules.php' => '0d8b16923fed57bebb615b87abe3b81c783676a17957d9290ed91e7497714728',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Services\\FleetService.php' => '15742261f1c6b5e642ec6230c03142649b8ee1d567ae7e72bdcf56c6759a0634',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Services\\MaintenanceService.php' => '171c9cd291163751db85184dcc842c1a055534e51a222887159c195014e5d51f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Transport\\Services\\TransportService.php' => 'dc765cad721b3e6b6c334b95edcdc32926942418728669244557b72a9ce9085c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\DTOs\\UserData.php' => 'aabdc581b342ce1bccd98fb159f075c8cd1080f112f6c805598b472b32457d2c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\Enums\\UserStatus.php' => '828b12d9fdae1e3c08436c5e06eb1951e473825e321993c952e138b170e8afa9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\Events\\UserCreated.php' => 'b62c240cb0d1bc4fcb228dbe55a0abcc1122a1523b02ef33ab07426125c619fa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\Events\\UserUpdated.php' => 'd87ee22258b748bd41edebcc95ed83b810583f771323b5c6b4ce5a9c9141568e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\Policies\\UserPolicy.php' => '7ae009ab6dc922638530c33649ca7ab2d96ddc552e78c7c6ecba3191086137a5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\Rules\\UserRules.php' => '66c9fd4c5adb155c4a10123b6b0e8017caa55d5c17cb17994dbb96b412d1991c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\User\\Services\\UserService.php' => '1e8a74151ce522fd054baa6316f8ad312540d89980f531f8de0db4af0e9a1d9d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\DTOs\\AutomationRuleData.php' => 'c111f275e0e443dfb45719fa13df0c7d15a1e9ed26c9823707064ec0198d7962',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\DTOs\\WorkflowData.php' => 'e497a6b74268400c6b31c15c53347cd9774f1a80bbd0f4df9fdb8ac1953a02a9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\DTOs\\WorkflowExecutionData.php' => '54a8ab22aff61c8d84dada254968fc1821e8813599083ff8d24a343272850cad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\DTOs\\WorkflowTaskData.php' => '7118db518efe18439540b7b9b8ac0704363eaa5fe46293e737e53574fa086a6a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\DTOs\\WorkflowVersionData.php' => 'fad9ad64db798d47b54bb412ae2f885dd7c65123a9851f5b5a32f74f505d9cc4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\ExecutionStatus.php' => '492c3f93d5174b6ac8bc659fb919ed3e67b1b4a012fe1006e27941d9ede2ea6e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\NodeType.php' => 'e5af5517fe9cff18c8e7712337382eba958d8c3415f6dba8365a32802127fc97',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\TaskStatus.php' => 'fbd84f705e4e46c68d7ec1bb47c854c464e7b4da7caba64bec2b112647a282c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\TaskType.php' => '58502ec9b1159fd6e6e29ba8c22b0c282c011ad75738258d7d4b63b67e1e22b4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\TriggerType.php' => '20259d826b52dc0c77016e6619003275a3149199a74d8ef37af2225dc76ff156',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\WorkflowStatus.php' => '315dcd610fe5402bbe926dfce76215744d630f80f506f4ea4a8b17b1519a2d5b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Enums\\WorkflowVersionStatus.php' => 'ef25e6e9ced605456aeff6e487efd94430cd05ac942e41e8991a0e1c50b4c538',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\AutomationTriggered.php' => '0dfbe2ba7447b9b96f247b68671b68af210829b9daf27957fcb9d4c0b2be831b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\TaskAssigned.php' => 'a1cb7bf9ce0048a41cae5e847ec0f27e470f0946c07cc92f4353131af8dfde3d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\TaskCompleted.php' => 'ef67a52e0218ed037fe3a1a9efe06658d4d54404df6fd003c80b7468e69732ac',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\WorkflowCompleted.php' => '6265b0bf6aff2e3a8dae6e708d52464a0baaf61755c08a42bbe85f6ac770155c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\WorkflowCreated.php' => '46954f64250ae32cf5eab38ca6dac2276ffb6b4c8e3698f08a8b52b23064cdfb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\WorkflowFailed.php' => '8c016b57f3be8cd4128136fe346a6f6e70eb12fc2b25f0a76640287a18073298',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\WorkflowPublished.php' => '4c69004400a74adc90c014e93325e082b1303b5495004f5cb1b6e583c226322f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Events\\WorkflowStarted.php' => '1975628ac1e3eb1deda478dd3e50e1c3255c931e0bad8872dd66dcc903a94738',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Listeners\\AiIntegrationListener.php' => '8db3ae4a3052ec6cb9d07853829f2bdb37e3a5df3345c07db761b607e6460929',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Listeners\\AnalyticsCollector.php' => 'a7361d2c8fbfcf7edb8437457c11d3099d8ad28a0d3c3fb75a725a8a93ef46ac',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Listeners\\AuditLogger.php' => 'bf0f1de9d045276a9ec98fde99c1cd5f796d0db337fc7f24ca307ef86d3e1b37',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Listeners\\NotificationDispatcher.php' => 'd4ca4f6254a0f34cfe9228d55b9804759bdd35b3b60c9525a8dd83d15f528843',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Listeners\\QueueDispatcher.php' => '9d0d301986f3dc47a0ca91aaa7e387bcfe20dcdb86cd1cd885f98fb834f425cb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Policies\\AutomationRulePolicy.php' => '5fc114d3444cc38143f75470bce8a649b8ad5d47f5ea4527972a90199f237c81',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Policies\\WorkflowExecutionPolicy.php' => 'b5053cf412872ac12d9ecac729f72f7c38303ab45f7b5bd5af4a3143b4c31b96',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Policies\\WorkflowPolicy.php' => '32457efcada9a1c1dd5d7f6d432173fa99ea034c1529011756f233c3a3e536a6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Rules\\AutomationRuleRules.php' => '68bbd27d1740a6858f103b46e9e8cdd8e093f8e41bd638995d600a7e03a2437a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Rules\\WorkflowExecutionRules.php' => '5dc0f0bc78c581ee2a5d546e95f79fa33f50ddffe48cbb838de8169774663567',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Rules\\WorkflowRules.php' => 'fb612e1ed6ba1e12fd24b9bb7bca1228582d877d01f2f6db90d65fb34ec72b39',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Rules\\WorkflowTriggerRules.php' => 'e6c1f5f12db6d8d6ef26e96b5d1d4f98515e079e81e0a1fe2fa383296f05862a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Rules\\WorkflowVersionRules.php' => '3e0fc2d37eb71bfbf17496c277dddc563940229201771960d2c0186b558330fd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Services\\AutomationService.php' => 'e892f5fa0d1057a1fd863ca1a038055af8f275129336b66701d15fe6921718b4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Services\\ExecutionService.php' => '50b7570951406134d9d0f0181a526ab5253cf67a6a89dd89add2ca0e5fbc9f2b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Services\\RuleEngineService.php' => '94efa4f6cf1bbad195f8e41892971bb6bab5f6918ebcf28b5bbe747156e27cdc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Services\\ScheduleService.php' => '764539830b57f34125374c681fe320458592a7b68dae72d161f3c4746fee519a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Workflow\\Services\\WorkflowService.php' => '82249f1381af94d07f3516d71ba3788b1c0986887e7d5b3735b060bbd1b84230',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Exceptions\\ApiException.php' => '302b92c0cd592d571b8adec464719158e648f665fbd787ee9757c4e107bbfb71',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Exceptions\\ApiTokenExpiredException.php' => 'c8af2da0b92404fa41866f7f984115883eae8fc69e69cd382db370cc690a521d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Exceptions\\InvalidWebhookSignatureException.php' => '5204e7daa573641d51708f8ccae302cd48c82f0af5be57539182979f7baa4292',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Exceptions\\RateLimitExceededException.php' => '1c064b373645ed10c505f1bfb3daca714ea09fc9ea756c0143ab3150cc99f18f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Accountant\\Pages\\AccountantDashboard.php' => 'b9c159ceced4c2d97ea8a63c84635a6adb04919803a8cfda7155cb6f53b86c8a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Pages\\DirectorDashboard.php' => '005b9dbb40e1783001ab03af9adacf56a3d90820ae201f1631f52bb60ea3509e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Pages\\DocumentCenter.php' => 'f7a0b2609745f26cef161f47b2e900cc232d4f4361217ca94d1bb9df6e1746a8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Pages\\SchoolIdentity.php' => '0da996813aa195d140e51247ec61d9f210236da10d9edfbda2e8edafd3f2d891',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Pages\\SchoolWizard.php' => '5eabe14bdd551c189ca0bb10fbee731df59eb2677b9b4d92e752fcc24423b985',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Widgets\\AcademicResultsChart.php' => 'e564f3cf5bb9ce1c408e6023bb03a880e87d765fbf3afc7cdd5188ddc41e3e29',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Widgets\\AttendanceChart.php' => 'b053bf9fe2d44a6303c830050ee3a0e5323c9ba6ff668633659e2373d4fa35fb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Widgets\\DirectorStatsOverview.php' => '2d244ceab85336465b43f91a5259e4ffbe0680f0068d5ffe8b5c15de76c10b5f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Widgets\\EnrollmentChart.php' => '2d46f321b091356d01f936e8a0778a0676c9fd914dcce7b4f6e97d522bbbf766',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Director\\Widgets\\PaymentsChart.php' => '3b30aaef4bdd4f65cc36fb66442396c80b46e08497134f8b56f651f20cc9b9a3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Parent\\Pages\\ParentDashboard.php' => '2ad1dad05cc5d5daa9af1f6dbc46925de606fdd5a97113a8aaacf8716c3b65ad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicClassResource.php' => '24f76803d8fa91127a926e6e4693ec6c1d1f230bf701ff42c8311449c98f90f9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicClassResource\\Pages\\CreateAcademicClass.php' => 'f8c1474d6cf7818aa4bc49ad9edefdc7971d19649072edb81bcd41441bc1f3f6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicClassResource\\Pages\\EditAcademicClass.php' => 'eff1bbf21350e40a666324e9d22270082e7478689a6f99ad37bd5003da4c8afe',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicClassResource\\Pages\\ListAcademicClasses.php' => 'e2cdb6dae558e6e5e389007614e969efa242c2f8a70f21070dc546ab888a0239',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicTermResource.php' => 'a1c34335aed46a2287a8be335efb27a9f421b825e5ecf612b36789d42d7c1e56',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicTermResource\\Pages\\CreateAcademicTerm.php' => 'ccbc73718a26dc2906f0707835e00f7fd54c87898346a1832ed04c644fe73eee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicTermResource\\Pages\\EditAcademicTerm.php' => 'c4a487d717898ddf765e9bdf193d3a0a64f142db63de846fe813dd56f6adab30',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicTermResource\\Pages\\ListAcademicTerms.php' => '53a17ecaba30b9c7ddd4a4de792d97c533592b8db72dd71f06210cbdd750472c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicYearResource.php' => '41c91cd02655736893e8442b212f6a8e108a85363dcb28c61102874fdf1859e2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicYearResource\\Pages\\CreateAcademicYear.php' => '303fdde096c892c4133d1f5b2577f8e4577b3b2a5121cbde8ded6c00ead0454b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicYearResource\\Pages\\EditAcademicYear.php' => 'c98f7f3bd7f0f6cc421b12c2476743263cd5d4bef46fb84c1f8e69f05011fae4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AcademicYearResource\\Pages\\ListAcademicYears.php' => '2518657b54ad479b0990287b22b5ec2762f70474d0a981853fba8b88ca916ab4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiConversationResource.php' => 'e1c45c236c537473f785f9ef8e58d173223c43c57a8688fca6f7bfd3414eb3c0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiConversationResource\\Pages\\CreateAiConversation.php' => '74269db5466d0bd6974d8942f549bb5d2f4a10f964adca4bd64e2ec0e43ef8b3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiConversationResource\\Pages\\EditAiConversation.php' => '9d5f979d23ac0dc9ff9568e79ea11daa5a24bc3d261fa5f28f2dee7e526cdffa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiConversationResource\\Pages\\ListAiConversations.php' => '53130b999419655f2aede7c572f84e03005e7dabe229880a7af7b49b37e21693',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiKnowledgeSourceResource.php' => 'c47cc3547e6419fc0b5dcf48fb5259715a7a3f22e65a6f5f43d8ff4b3b8971c5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiKnowledgeSourceResource\\Pages\\CreateAiKnowledgeSource.php' => 'b0415aeec5441febe306fa6005ad28db5d660e866cd6fc42b561f231e21904c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiKnowledgeSourceResource\\Pages\\EditAiKnowledgeSource.php' => 'ef2a3d4fc43f4eaf1c428f848969c567b7e8416f7816b2b5a8b7b376a21803cb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiKnowledgeSourceResource\\Pages\\ListAiKnowledgeSources.php' => '39240cc99bb0d95204e4a40409eed510df985e3a65e8dc03a62f0d2e9d871205',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiMessageResource.php' => '7cd7f9805f991c5b76300474d687a856c70c47247c6463beef5b5d3cee8b6d40',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiMessageResource\\Pages\\ListAiMessages.php' => 'cd16ea30c4d4416de20c8d007d27c7b6326553f75d5fbc1a5720911fc478d27d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiPromptTemplateResource.php' => '58410d5faa0770614b62022a0a8651bffc0b27d2acd2f9889304b7d1b3717533',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiPromptTemplateResource\\Pages\\CreateAiPromptTemplate.php' => '88f72f247e405d150a9d1dbb6f84dd27d8651c29cea460d315e513a54dc26754',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiPromptTemplateResource\\Pages\\EditAiPromptTemplate.php' => 'c9a1d9d913cb641d9847e83676cad4dfbdfbbebb4a6feb9874e019f1efa337a1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiPromptTemplateResource\\Pages\\ListAiPromptTemplates.php' => '5f30402dafb415d3ecef8e02da9117744462e0efe890185bba86726bf1d584a4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiProviderResource.php' => '156e9febab74d90a064218956895eabcd983048e92e7d0d9a714da7b16334b94',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiProviderResource\\Pages\\CreateAiProvider.php' => 'c81248cdcd29ada3a682661fce57eace47d1e1ab1d00b1b12e5127794ffb34da',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiProviderResource\\Pages\\EditAiProvider.php' => '82efc8f21fd3c29ef96f488cc03d6e211a02aa8264a41d0db1c4d6d3d0136b67',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiProviderResource\\Pages\\ListAiProviders.php' => '1b6b458a3568e70e46d5e3ad0a993b4e00652b47c6ef05f6037810d6cf540553',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiUsageLogResource.php' => 'f1c595f1e1e17f7eba2d781e7d6f7b74ee5413a0ff864a53947ed69fa7a60e21',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AiUsageLogResource\\Pages\\ListAiUsageLogs.php' => 'bd8413f4387b497c1ae14d5d57d9e02234896b2d2c367487a04137db595c53c1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AnnouncementResource.php' => '3a134d1a03fcd728e6a02f71932ee7a3ac4e7d395a033244ebb603460ad37b0b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AnnouncementResource\\Pages\\CreateAnnouncement.php' => 'cb90344dc4cb8e552da10206c00a4ed152ad84e31854785e3d378442f48b6fd9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AnnouncementResource\\Pages\\EditAnnouncement.php' => '9a33a13a7d767f48e37f7a5ef4bd31622efdd69a65f7f14ba13ac13df37ff454',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AnnouncementResource\\Pages\\ListAnnouncements.php' => '725c9615e3114a9fd7df6ce1f31991151af881a1338ee521ad5e69066c8e6322',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AssessmentResultResource.php' => '0367fff9e1beeacdb315fd69215503c1c0c9c98375aaaf5aaee6be15a8f69065',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AssessmentResultResource\\Pages\\CreateAssessmentResult.php' => '1eb6b53d7939a550698674d2d68682c8c07f7a49396ccc83e80fe4e42989dd2a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AssessmentResultResource\\Pages\\EditAssessmentResult.php' => 'e9e2024f29c7b9e0aa92fa21160b1712d45f37d0bdb00a30089b6e601c0176f5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AssessmentResultResource\\Pages\\ListAssessmentResults.php' => 'dfb9d99ccd88048e0ef6bd148af6529a9a6a8de322d029e653978ef42944cdde',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AttendanceResource.php' => 'a238a736553901c57f6c91c0aa0bfd71f7581642cf1bea00d8b446cd98118f88',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AttendanceResource\\Pages\\CreateAttendance.php' => '7db3ecad32c9352235ac45d06ff313430c35c741c78a2d85e61348cd24fa7681',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AttendanceResource\\Pages\\EditAttendance.php' => '559e7dcdaa7642dad284d0ac557bfcda85ed45a2003977c79f14b71816b2e430',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AttendanceResource\\Pages\\ListAttendances.php' => '45f797074034bcedb964a2e3403a9399d7327ec311bb0233df9a0a1af6b44cf6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AuditLogResource.php' => 'fb7393541f10b3bdcd7b910799d0b8fcba328f7d7f72224e8c45143e7c8822f3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AuditLogResource\\Pages\\ListAuditLogs.php' => '1c6c5647ca8b98263bf90e4cda5119ade329504e064bbd526110843f013467c3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AuthorResource.php' => '4132b456a1ee0d21edf574dfb8b5314b8edd52effc18d784cb4131a1c9efaea9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AuthorResource\\Pages\\CreateAuthor.php' => 'ebaff20d043ea17a9630b9a363fec9ec8592f88a0dd95c82af802a9df6a1af8e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AuthorResource\\Pages\\EditAuthor.php' => 'a943fc89bbb4d6b208fc4e5afc0bb1e735f1add9050d2fb9e9e1ad6e65ac9153',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AuthorResource\\Pages\\ListAuthors.php' => '76a9711670bd290e89e252c2fc06c1f8ab486ef8b8abdf5665c7376520dc0725',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AutomationRuleResource.php' => '7bf4d5f6249868fcb94214428bc9e8aa9672df6753cbe87282dbfe2fcd87a56b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AutomationRuleResource\\Pages\\CreateAutomationRule.php' => '648f2ade7b79dad8dd1497abd0e023e5ae48a9fa3239cf6a9c75cd64f300afcc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AutomationRuleResource\\Pages\\EditAutomationRule.php' => 'a0b33815dc0c36346fe173fd5b2be1a1f73bf9279cf3e8b9182de310074e3642',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\AutomationRuleResource\\Pages\\ListAutomationRules.php' => 'b7009e05bb1287b080eb71cf3452a430f59ce28a665ad8c891a0b36561dd4e94',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BackupResource.php' => '5c0d336dfe7ca581ece8312f1aeae2382505d759a744156fd349a0020e451a72',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BackupResource\\Pages\\CreateBackup.php' => '49b0f781713af6d05ba9eb0e3acf5f69836d53f7a5ee24350011f85aeb249eff',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BackupResource\\Pages\\EditBackup.php' => '71bda05deccabe8fd612c4b3c85de5f4849da55a07f0ff6970fe06301c13dada',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BackupResource\\Pages\\ListBackups.php' => 'c4eea1cb4e8210d20b0b1b20339414ed395e0aa6b58c88d3c04fb8661e762c79',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCategoryResource.php' => '5e08fdf7153e05b7eec17cfb72286dcdfa0a73dd3a7d34e197cb107f158c4cf9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCategoryResource\\Pages\\CreateBookCategory.php' => 'f3bc2d45ebc64f1bc8596110c635f3fb81a85f6cd051865fe4724870e982c648',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCategoryResource\\Pages\\EditBookCategory.php' => '55b2e7975432bbc7f91d2adddb37b7777c4842850e0fb4510d18e6c5cc7c50fc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCategoryResource\\Pages\\ListBookCategories.php' => '91e9b85799bc8ce82f2d5c343b2bcaa6658c7e0efebc75cd381606b9f6b1473d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCopyResource.php' => '9fe45a204d23adfb02a0bd88dd90aba2e5aa5e2aec296e368a2fc352479530a8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCopyResource\\Pages\\CreateBookCopy.php' => '21b3bb91dee4a86f8e263aa1fbd2bc6dfbf79cd82b7e8a92e017becbb6d3ca45',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCopyResource\\Pages\\EditBookCopy.php' => '3827f963165da321c8c524ce740b3efd802e8d6e2b113d59b968f05f45cb8a56',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookCopyResource\\Pages\\ListBookCopies.php' => 'abbfc7010b318d41c849ab26306a718829b99db224de089e47d2b51b92b18aa4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookLoanResource.php' => 'd7bca814604a4eb7052afbb1ce072b9c7c51cf70777d3378c812dcc73f290a66',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookLoanResource\\Pages\\CreateBookLoan.php' => '9b2239d337d7bec82ba13827d2210daf4c9c2573e26eafe16b63ac4d4219b83b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookLoanResource\\Pages\\EditBookLoan.php' => '7455e40646fe471c221ade1431cba24cb0c327391df6e48c2f88b377d3006428',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookLoanResource\\Pages\\ListBookLoans.php' => '4bd32dee279eaabfcbb044ff27f4cafd16347024c8ec054693b31854101c9555',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookResource.php' => '780e6b28f72f2eb39da19e85019c442f463e734f9870fe7a8eebb943e32e2d52',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookResource\\Pages\\CreateBook.php' => '04cbdc3a4ea37be81b68a2b0bb610f61c92ea75249cb7ba24787146e2382a7c2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookResource\\Pages\\EditBook.php' => 'b3f41495bfa5d8381989cec1905d982d5472eaa68ab75b77905fec18dfea3cc0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\BookResource\\Pages\\ListBooks.php' => '5180727f4d48bc8cde62043c625d7102cfef26881f577602efc2574de439e894',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CampusResource.php' => '20376b0c8917bb7096318965481cd8c1f8c4bbabc1908ddd010af26d3fe2b193',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CampusResource\\Pages\\CreateCampus.php' => 'f3b656093d339b81a4f4ab1092ce48e6a374356289f8fc90650b257501d65762',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CampusResource\\Pages\\EditCampus.php' => 'ab89633d6af7f7de4645bafb07d383565a57da51f454f8f36be6f051798ccec3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CampusResource\\Pages\\ListCampuses.php' => '81c8ac0ca678da40722d9f5f500504cabe19fa752cea9a7d4c033c6fd105653a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CentralUserResource.php' => '3cd670e06873d250dc82343f0fe477294befb8a9ca982e23de067404f80097ae',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CentralUserResource\\Pages\\CreateCentralUser.php' => '3680d0092af99c041f08672b376790d688265c5c823c75b23a1399c83a3badeb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CentralUserResource\\Pages\\EditCentralUser.php' => '4ecab5eb6e1153b47cc8b03c4cb6f753363b067087a56e15d3858e7ab4026645',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CentralUserResource\\Pages\\ListCentralUsers.php' => '52d86b5b1aa989b7242180162aacf0120eddb3c14b0db6e401d1f56851cb1f05',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ClassroomResource.php' => 'd0dfb188428c4c03f9eb75ff8c0dfb5bc826f602dafe8db33d149906c2345b2e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ClassroomResource\\Pages\\CreateClassroom.php' => '350a259e9bedf21422a524a83c7b5e46880af7957fb032fd0fa502305e43858c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ClassroomResource\\Pages\\EditClassroom.php' => '763a255ee70097aa5faf77749db456dc1046c1f4019fafe9f52ecd2cf94e96ec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ClassroomResource\\Pages\\ListClassrooms.php' => 'fb94db0ccfc9dab110e3f1fbfa0830d80e2e925bcf8be6b1b614c5b6bddb061d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CommunicationChannelResource.php' => '5bb4a846befb6e55825e37c5aa66c1c2bbf20148bf04f92d6d9f4fc5029ad794',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CommunicationChannelResource\\Pages\\CreateCommunicationChannel.php' => '6c052c40a099ceefa2cd8177e93b5962130549de2eb9953b9a29e46e3f78ae76',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CommunicationChannelResource\\Pages\\EditCommunicationChannel.php' => '6aae338ef927722964b6195cc612cecc9d237a5ff71c6506cd107c640ad92c2b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\CommunicationChannelResource\\Pages\\ListCommunicationChannels.php' => 'ca142435e6c993910056a64681f3366664087bfd472185d44388fc76b78120fb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DashboardResource.php' => 'ba4104ef24eba8ccf9975f357f8668bdeccaa1531aa10805269d0352f3b73f37',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DashboardResource\\Pages\\CreateDashboard.php' => 'e103ce53114d58e3da6f951e6fa3d6a6c2f590ab66d4adab3ddf379afe1b54e7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DashboardResource\\Pages\\EditDashboard.php' => '55682e14999cf9a04d78cc253b66f8150bfec3b41c860b9d78e19a9dd2ac21d7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DashboardResource\\Pages\\ListDashboards.php' => '5666531b77b8d3819b9a3b2af3faa711915bf19020ece70112d874aaa4e5a6ad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DashboardResource\\Pages\\ViewDashboard.php' => '85ea9c61e48b7e5d981165b184604296c783033bc47ffcae3f5638e2df199982',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DepartmentResource.php' => '6bd732e31c2f8c00a1744311bf9f1fe464281f3eaa470a1f2a317bf4e618e5e7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DepartmentResource\\Pages\\CreateDepartment.php' => 'dd776eb96036ca7a4d19ecc3c72f7f78cfe40b689c0a02ce28c8bdc729923b47',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DepartmentResource\\Pages\\EditDepartment.php' => 'f375524de596032da3bbfe2bd314055ba3c8ee45d118db1d3dd64e855c8f9aef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DepartmentResource\\Pages\\ListDepartments.php' => '4595fa10bbeb7285a0a02a5b954a812b8033aceec3ca4e982bfca344ed9dc6ff',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DomainResource.php' => '0c451029daec1576852b5eafd8038995e07368f32f0d0ba3cd7847424089bab7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DomainResource\\Pages\\CreateDomain.php' => '303050ffd2e5331c2dfdc67ac4a962a4003763b32de1627c040b04e29f5bc0e6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DomainResource\\Pages\\EditDomain.php' => 'a48df4d9bc97231e8d96b2e7d9dbea5dad2eb9e434f7c5f3ca5b83f63cd16d50',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DomainResource\\Pages\\ListDomains.php' => 'b5b2f6b28a029f596b70270c3fe87e4be17e13271ec2f16026c2a38f0352fee3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DriverResource.php' => '2f1be3252e0a9bf680420be1c40fd8d99883347541948fd4098d732f65c1a9af',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DriverResource\\Pages\\CreateDriver.php' => 'd6e7c7625e6487a6279f8308061ccff68e36e6e9e547bfceb4fdbe62bb81e2f1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DriverResource\\Pages\\EditDriver.php' => '6bcf84abb54c8219cf1c7c9ee90a6b41892f10915c3775fa25bbd77f817d36fb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\DriverResource\\Pages\\ListDrivers.php' => '6d3986f92a0e62e3b313ece8bf750e8f822f8efcac487238ccd01945ed0b1633',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FineResource.php' => 'ee6a20cb27b649b4fc5ef8047724a3051e6bfcb936cc2392db872b9c71681940',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FineResource\\Pages\\CreateFine.php' => 'ad7cbe613f0af6123cdeb4d4583d1fded4ff04a590ebfa62dd791809dc855c42',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FineResource\\Pages\\EditFine.php' => '249487e952d9d409083a60f57f616ac2e3004ac5f76cdf89b8d624f3d9e967b7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FineResource\\Pages\\ListFines.php' => '9ce8a488561e17a57c9f514cd2abfcab08ddf96679d2edacc2c425d52ffa8941',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FuelLogResource.php' => '988a7d68496f7d7e2342ebf4be8d72d57d3ed84bc7d19e0eece0b3a226ec9ff3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FuelLogResource\\Pages\\CreateFuelLog.php' => 'cba5ef76fad79bcfbfe8604327c58fd084c75c18cc70c2fc4fec533048e099e5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FuelLogResource\\Pages\\EditFuelLog.php' => '175b302a1e6d0f10fc946353ede40b7eeb44e5da4dd5460b65cfedcc408d1cf6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\FuelLogResource\\Pages\\ListFuelLogs.php' => '9577fc27db45dd44bebc460c783662327358abcbbdb7687a921697c90fdacfe1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GradeLevelResource.php' => '26d2ba688de3a0eb3c972722c2ac9ba63261ba20b31dc5e5c0a6e1103e69fa82',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GradeLevelResource\\Pages\\CreateGradeLevel.php' => '2ba2843d79890061a3cc4f4883bb953c93c282bc94449fbf0db8e04b4efcfd1b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GradeLevelResource\\Pages\\EditGradeLevel.php' => '27141e2a3f12ef0e08c91e007c4482aa22024e2b7350bb20985fc7c0000c88a8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GradeLevelResource\\Pages\\ListGradeLevels.php' => '4c3abacbbcadc144503f7182be9a61606de776a2202eb19146aa9ca27a5951e3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GuardianResource.php' => '4d7cff0668787d2c4ac2ef8745c8bb47a7b51040aab72d78f7948b8547293690',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GuardianResource\\Pages\\CreateGuardian.php' => '87b90b1930a323a91e5fe4dfc4e19ccdfcb650f1d4da44ae7dd34c6b840cf465',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GuardianResource\\Pages\\EditGuardian.php' => '3a80a5576eae4c48b9e7574a9173d76d96d7c8e8ea5f274cef20be3093fe7685',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\GuardianResource\\Pages\\ListGuardians.php' => '29c126010a2cfa4b83a3d38a85c48175292fda95f2f14ce5ab21b95e30499e86',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\IncidentResource.php' => '4b1b42d3453756d0cd9f4e90799ad9ebc87c3b7706a19b21342a473f942a2afc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\IncidentResource\\Pages\\CreateIncident.php' => '3c634ca3d29cfab21e6e4aac00b9c17ceeab1a981d197cbdeb531108187aedb6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\IncidentResource\\Pages\\EditIncident.php' => '9243cd1b1ed488fa6602ae05a78923500473bf4a20f2963db2a69829aa624f89',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\IncidentResource\\Pages\\ListIncidents.php' => 'd44b4f9353a463a0a356903281c0690e90bdeb9285ac21c8153fdcd0009a601b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\KpiResource.php' => '11a7db3f64ac28820e1a11dfc9c2c3bbdc6637b24baf6a8f8632199fb84b0ff5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\KpiResource\\Pages\\CreateKpi.php' => 'cf14b9e02c713f1f3b8431b4469b8ca802bcf9faa5b389647a7f8ea5c8ced5c1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\KpiResource\\Pages\\EditKpi.php' => 'b90ee61f551308be543467ac06d4355c95ff991d89eae29a4f9526682d755ad5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\KpiResource\\Pages\\ListKpis.php' => '885fdd8761051d70002c5f1721f51dca55f8696d52c0fc60bf3c51574af2a125',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\KpiResource\\Pages\\ViewKpi.php' => '6a2f9f204321ecb3cd92356e48417f3ae6a6b1296d39fe554eea89084743b49c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MaintenanceWindowResource.php' => 'd72d981447c54a2eaa1a9e7182e65ee7071000e3227da55fb7f2f4db9a0ec1ca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MaintenanceWindowResource\\Pages\\CreateMaintenanceWindow.php' => '9bbb537998acbaad0f81544813b34d67f61fc310f669cbb51477a27fbd47f2fa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MaintenanceWindowResource\\Pages\\EditMaintenanceWindow.php' => '677bb68494843d33c4ec7dfdd201fce54f69cba06bd329d5141a3e5e8c9d3e3d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MaintenanceWindowResource\\Pages\\ListMaintenanceWindows.php' => '441ec0398a82fd0bd8b453d5bd59b901a06764fdd9306bfc343d270b91c6aa6e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MessageResource.php' => '4c73b123a869d1eaabe3cde1f66963ba5ffa3c2796739c943c3141062635d5b0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MessageResource\\Pages\\CreateMessage.php' => 'fbe0c119914c2a17220fc3eac612d677d42f9ca66eaf3eba4e7b2b1b264c22e1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MessageResource\\Pages\\EditMessage.php' => '2a4fadfe522fda01917bc8ada1d323b805d1d3047a429685c612a3bd5f83cdbd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\MessageResource\\Pages\\ListMessages.php' => '7e54c43262d7a00127382267b2e2573a0726019721eacbc2b3abd8754cd6b488',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationChannelResource.php' => '4b286cd6359e488c5fc2afc30d6bf56ac51fcf05b06c0089455e04f601c9588a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationChannelResource\\Pages\\CreateNotificationChannel.php' => '6c6c021c1d27c771a5c5725e993a86e35259d627eb78d95fd57383161e9bd17a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationChannelResource\\Pages\\EditNotificationChannel.php' => '2135ccd945cede17f7d37dbd70ed9c3e9e166bbc744231f8d05a65568ed6b0fd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationChannelResource\\Pages\\ListNotificationChannels.php' => '628cf1011c4d011c611b96d5a77fa8a32f75562c3f4b560eb84ae65f055e1a12',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationResource.php' => 'e94379d753b25a868ff013bf81e811d9d8d734d31dd54d15f17eaa1da91440f2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationResource\\Pages\\CreateNotification.php' => 'c668bcab1550c584ffeaa8ce4720fae7b399a2796d301af6a76f7ae88cd0a1cf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationResource\\Pages\\EditNotification.php' => '88744e47ea541cf1f4a54868a5098601f9951b875713d9d6ef9665678f32b3b5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationResource\\Pages\\ListNotifications.php' => '5f0ba0f74b421da2e7f1bf902f00e01790bbfe78e27eb53a20f58f07a08d8d68',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationTemplateResource.php' => 'e543a3522f2f7b25f8cf01acabc09af0b63a57ad701476f4aa80f0079931134d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationTemplateResource\\Pages\\CreateNotificationTemplate.php' => '091d9a172bf3346e74d3e9f61c2926d4b2778ba762688b6dd2d03406e09b663b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationTemplateResource\\Pages\\EditNotificationTemplate.php' => '8c4adca17015c2c356917029e4bbafd1f378fb7880d68f0174df6636c98ff17e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\NotificationTemplateResource\\Pages\\ListNotificationTemplates.php' => '1465a02897a94b48a69c7fa5423660e5b4b35168cdc7079dca595cdd22b086d6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PaymentResource.php' => 'f0af9936cd3a2877acc0778323b7ae10f8474abae49cbac2fb1555265de04656',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PaymentResource\\Pages\\CreatePayment.php' => '177e35c529c31b59357f5a2d5b2b87bb10fb6fa9406b2d72040cc6362aaf08b1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PaymentResource\\Pages\\EditPayment.php' => 'ef792f9935c05620ef0c12ce0881a666cda8d72f27fe176c60d0c0f64ced05ca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PaymentResource\\Pages\\ListPayments.php' => '51457cd44942ee1f7f982a5939bfae4b0f04cd267a1eff912eb8433577460611',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PermissionResource.php' => '9b79000419319e4d8f7944355478633e1fbcb409dbf055aee3419601bda9457e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PermissionResource\\Pages\\CreatePermission.php' => 'bf51c0b8f5077668039d0a5eaeb8198ba4d4832ee5cb98d07f39ffa7dcfefedf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PermissionResource\\Pages\\EditPermission.php' => '69d017eb52b72b2c1d8a3425a30e0fba0fdb607f7c42ff6db34bce06f2957e91',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PermissionResource\\Pages\\ListPermissions.php' => 'c3bc5abe3895a6191406c647598a1fdd3ee14d58145d5d107aa69f148a9fcd88',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PlanResource.php' => '6b2f612321df0c82521c99a247132cfaf79fa9bdc72c8ca264d8bfd67d659e5a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PlanResource\\Pages\\CreatePlan.php' => '74929bf6cd08f729ba947265d1094dd34bf9b8a1d327bcf4e295a931d3a6caa2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PlanResource\\Pages\\EditPlan.php' => '6a368bdabd8f9ef56a3893c0813c034189acb44f2209f3dd938ed9401f4eee20',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PlanResource\\Pages\\ListPlans.php' => '8838cb657f1a4364dc3775e7ea11fed2607ffdeb0f8c736da480e54ef0580266',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ProgramResource.php' => 'dfdacc04e5ac7a064fab7e36ed3cef5439aee44f664a2000de79e65a84475f06',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ProgramResource\\Pages\\CreateProgram.php' => '85d7b9fba2ca48906a63b12841fc64326bd8a0524a60424e8cddf2d1997a84a0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ProgramResource\\Pages\\EditProgram.php' => 'c372b852dc7ae0d8a0348e4bf1744e6f3fdba8ca94d0649908baea1c8903f5fb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ProgramResource\\Pages\\ListPrograms.php' => '375f70598f26ec9105e93a0f2034b30f839793bc8d85b0227a2540bc24ef3c58',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PublisherResource.php' => '655dc7c16eee55d55611275ac05694f2bac7766aae9f8f1ca69f65b29cb657e7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PublisherResource\\Pages\\CreatePublisher.php' => '90e5ca4127414f33f886b202360882a7e50d454c3df6d8e9fa15279429f37d77',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PublisherResource\\Pages\\EditPublisher.php' => '882858c8eaef508515386b8658ead7de440fc1d4ff27584b2644453ee40294f5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\PublisherResource\\Pages\\ListPublishers.php' => '15caed04c682587010381ed878cd113b55d5f78629ed0c07a34d0bc8c72620b4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReportResource.php' => 'ea50989e99de1206a393af6f0eba0d86d1e7de37dd0198dc76841ec694428638',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReportResource\\Pages\\CreateReport.php' => '3ef43f5432665dc47c03dbaf8f031fbd8995c8de59d0a0e42108b91a7d7e3042',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReportResource\\Pages\\EditReport.php' => 'fc391496e1eb0d52711dae75a9e27cef9d824d803aeea938f012f3bf4b1af9ac',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReportResource\\Pages\\ListReports.php' => 'c3d357ad0371224b974c50f122aa52885d79d2eb8d4c2214a6c2a049e8eff6de',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReportResource\\Pages\\ViewReport.php' => '7a3b9daa20baa5af9b3e23e1957a39e12e680b9cbdc78297f19f6ea24cb01a3a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReservationResource.php' => '51842edcd4c33c6a22d3d3ec3dc4b2fb41d37c186c7f46853ba667eec77bd499',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReservationResource\\Pages\\CreateReservation.php' => '47ba2af4f0f458c3eb5394a2ab319f26706fccdf46617b53a566fdaf7a21b65a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReservationResource\\Pages\\EditReservation.php' => '980a7e135245f9a81d37d4da7073121e0f8f835a29ad6bb9e498048d51a2ba5b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\ReservationResource\\Pages\\ListReservations.php' => '454a057770e1fd6215663a9c02436f98354f6588a0680fcce0d4aabe86758074',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RoleResource.php' => '6f42495081d472ea0fa83c6a1bf920a96a6f7cbad66a226d5ff6172b825b1f59',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RoleResource\\Pages\\CreateRole.php' => 'f9cc0b15326eddd050285396c88d06ac80ed27da8dd1bec4c4e8916f9da11851',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RoleResource\\Pages\\EditRole.php' => '3d762ac8bba0fc49fbf733adcb6d0b451d6af47a8b8f4948c5a4cdd5fbb6fa5a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RoleResource\\Pages\\ListRoles.php' => '1de6c41a48c3f92dc6c937bfaa8487a0e099437d30102cac9c072d768a71c868',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RoleResource\\RelationManagers\\PermissionsRelationManager.php' => '50cea5de2656afada39cbbaa7be4dfe8cc5d329da272e4b42cca3f03da1c630c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteResource.php' => '70482992a9325fad3a74bb340cb5a2af5812196b103042c9054dbc0b4705fd85',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteResource\\Pages\\CreateRoute.php' => '93c13685be75c7722948dbb1556c58b6ac7524bc7b4564961d19b3de5cf12d3f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteResource\\Pages\\EditRoute.php' => 'b9d43bd53e5f1632a43777878c273889191eec0289e1217254afcc2dfa09d49c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteResource\\Pages\\ListRoutes.php' => '503c001fbd07172195fb995df38693c06bfb6d332304bf5f7e5238d6e9d15d30',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteStopResource.php' => '19cf2c82c4265c838e8134f1fbf75d77a18ef07503dbce8f9f9e080ea82af938',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteStopResource\\Pages\\CreateRouteStop.php' => '8091439aa7c86f34b8b73015380e9896417f5d04b0fc32e654c7bad174c78ac2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteStopResource\\Pages\\EditRouteStop.php' => 'd940844a5863075cd740d8686c323a7c2266d95ee62f82d24e5c41ea49db9701',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\RouteStopResource\\Pages\\ListRouteStops.php' => 'b4b3cecdca662c079bdbb79adbe768302573c04cfbb8fc201001cd28dd46f314',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SectionResource.php' => 'ef94977bc9fda0cfce6c6898296221a53dc3877567f23b4b023651ef8e108051',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SectionResource\\Pages\\CreateSection.php' => 'afc6b3111e0c174047dd0a436cea13f5cb983b62384661c630aac463a6335b68',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SectionResource\\Pages\\EditSection.php' => '342892ceff4c5dddb632be455e5689e10c36e1f335dc37eaba6cb2ebf1d1a1dc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SectionResource\\Pages\\ListSections.php' => 'cdaa07db5ac4ded4f99f6b467d823a7d91b7b80ab201e4e6d2bd476815602a78',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentEnrollmentResource.php' => '3fc131acd71bd125701b1f85310886107a545e6f4b82188b736bb59e2873fb6c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentEnrollmentResource\\Pages\\CreateStudentEnrollment.php' => '73f0d42c6d38a0a19eae3a6e1e6135f43453e301db538214a7d29612d2b9a478',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentEnrollmentResource\\Pages\\EditStudentEnrollment.php' => '000fd3431f6b20f8b393a7ddb8129d5b2201aa5a2ab9d8693d327bf63e89f9b3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentEnrollmentResource\\Pages\\ListStudentEnrollments.php' => 'd6b90603c1cb6cbc45fee26d5498cd9b9ce362c0bffce4019fad6da1b46a942a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentResource.php' => 'a5c74b60811a341c786152bbc65c051c2270377cc135b65ecc16e870eeb1ef5d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php' => '54bfafb9ddb9d4f08bbdf162366461c629d04604a79df9fe1c6bf00c48555d66',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php' => '2086d95ad74a29c337a35e68dc6401fe25508465ade48097967c5a7c9e9f09d1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentResource\\Pages\\ListStudents.php' => 'dfc237a9445d5f1736de5f13e9d95467e8c99f639c60a1366b9029aa6fbf5b91',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentTransportAssignmentResource.php' => '161f7245c24e304c776736b5dcc4481934d32d32f93236c747874af6be830bc2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentTransportAssignmentResource\\Pages\\CreateStudentTransportAssignment.php' => 'e040dc48675650398dfb7a74aeff7ecf4a88b67086f9e435202f1286eccefc6d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentTransportAssignmentResource\\Pages\\EditStudentTransportAssignment.php' => '4c17af4621237176567c22c5a4367cab88251c9c76dc2210a30ef5deeb32f9a8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\StudentTransportAssignmentResource\\Pages\\ListStudentTransportAssignments.php' => '1d47d86e6b8ccd6a18a3f329fdbc44e35097ba1c6b3f57d00d78d78a5bfa7094',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCategoryResource.php' => 'e9837c731f592cb0915a2804c82df64a18c68c366be2aa6aca16207be3c17b7d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCategoryResource\\Pages\\CreateSubjectCategory.php' => 'd5513391b53aabd781cee0da360867dcdf6f66e1d560edbe19d079cbc6b222f6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCategoryResource\\Pages\\EditSubjectCategory.php' => 'f96af46ec789698a4ec36436d345ff7e22d6eac5d7a6818dba1fb3e73bf76e0b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCategoryResource\\Pages\\ListSubjectCategories.php' => 'f53a5cde443550ac4cb5ad507ffc72fa893365025f6598e20a37ab595138268e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCoefficientResource.php' => '72d7df3fe9bfdd892dc8b2759d76f396e663c2662a51f59b488704372ad15dce',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCoefficientResource\\Pages\\CreateSubjectCoefficient.php' => '22a2bbe89bbc622be03b46ddd7bca1d4984f41c9672b8e061c9b188caf2e9fd4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCoefficientResource\\Pages\\EditSubjectCoefficient.php' => 'd5afd6b3ef8818daa430d3e79d8c2969869a4578d6b90c004362ea08e7c8e02a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectCoefficientResource\\Pages\\ListSubjectCoefficients.php' => '03af3c3c963983125fa0f5a2569ce7af509f3804aee56183c0319a006da2fdbd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectResource.php' => '79eddf07c3327e307d5cb4429b0ea1609881d7893f9ccd77910f0381e6471ab3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectResource\\Pages\\CreateSubject.php' => 'cee85a1f8592b857dec336643605fc15b655c21095c13a2ba54f2ce8f87a1ab8',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectResource\\Pages\\EditSubject.php' => '5fe83bf30988fde1f7da8fba0e1a445b920acaf0fc0ab3f88e954d0ac4ad6f33',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubjectResource\\Pages\\ListSubjects.php' => 'b0a858df61fa50460965e555ed296fd140503af0fe5140e55014a5f892b7e69c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubscriptionResource.php' => '8ce5b5fd3501e1429eff3a63034148d0091a320b33d1095bf355b8f63b76738c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubscriptionResource\\Pages\\CreateSubscription.php' => 'c5e8a0882d07c6239aa46259a14247acb900256ae976dad868d59234f471458a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubscriptionResource\\Pages\\EditSubscription.php' => '36a740878dda92bef8a963a967fa4bc0b97c83a32dc65f3416eebe4af56e3870',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SubscriptionResource\\Pages\\ListSubscriptions.php' => '76fcf4689966692474e2f03b1a4c27a1a02a6949118bcd7809de9dccb67d44a0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SuperAdminSchoolResource.php' => 'e30ad9fdc028fba735d234717f058e4a8c6342edecc0fed69d97859f6fc70ccf',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SuperAdminSchoolResource\\Pages\\CreateSuperAdminSchool.php' => 'c0443fe2c3e95f672b240c0846a9dc727ddec7bc9ecb2a9b4400ccdf7615393a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SuperAdminSchoolResource\\Pages\\EditSuperAdminSchool.php' => 'cbf71071c79bd491ca307a4ff5ee870d03ecf7807dfd878e31eab755b9401bd2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SuperAdminSchoolResource\\Pages\\ListSuperAdminSchools.php' => '308fa69612f0a622c7ba9ca1f7d55b17234daec7352d54726e08b2981e5d25ba',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SystemHealthResource.php' => '3caad594bc021f5d120494cb9b9c7cfafc0f2898178a2e52f9e22b19b2644d19',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SystemHealthResource\\Pages\\ListSystemHealth.php' => 'c0cf6e4052a2e6406b27dc0b3d8ac8307beab7be980b6eb4fdb4a958e37e2c9b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SystemSettingResource.php' => '9fca8c7e5d89ab37829f639f5a016d1d993cd2b672026634fb4440fadc4304b1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SystemSettingResource\\Pages\\CreateSystemSetting.php' => '4bf687e4d614cae9f131cc3ef488f5cd3942dcb42b5d59efeea3bd861c00c417',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SystemSettingResource\\Pages\\EditSystemSetting.php' => '26a26212dd0efa9f51158671a5eb334d0683b52f0196194be92d007c8d7d01cb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\SystemSettingResource\\Pages\\ListSystemSettings.php' => 'ef74d4e8ff39b7b72045f51f227675b88b4e11eb966ecf2c528ac3e95fa83b58',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\TripResource.php' => '9e1bf63f0f0d226ac0048bfbe2fbf7642c7049db339b95bf8dbf549bc41c7efa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\TripResource\\Pages\\CreateTrip.php' => 'd2db1edfb29bed0a03b5e44eaed0e670929df25c9ff04fe06f7de91b86457c87',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\TripResource\\Pages\\EditTrip.php' => 'c34914b8ce686cd5bdc326b905e6b5277e7f0d1b4276206ee6ca41b0fa3b1c9d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\TripResource\\Pages\\ListTrips.php' => 'f3cd69d699a28b061e4349402d37fc92ccc268544e2e40c5e13347121093f44e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\UserResource.php' => '62acb33b8436b33c58ad11625dfdfcd000374cf1b75ae8c811aaa72ab6f59044',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\UserResource\\Pages\\CreateUser.php' => '19170e25e6cd787e287997dcc21adefe7b1ace0b18939da48a1a0bfdf50c22de',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\UserResource\\Pages\\EditUser.php' => '4f0ff4f7989080008860e81e864bac3a7d8ed9094995218b1f53aed3cf6a65a2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\UserResource\\Pages\\ListUsers.php' => '10b5f34e408dda836bf8d9b88cadf883c2f5f546dc07e6f10f68271aa5502451',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\UserResource\\RelationManagers\\RolesRelationManager.php' => '02ed91e9a5dba537f0bbb2fcd15ce0fa5ba56129ce429ecb9e8a89f6c5228605',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleMaintenanceResource.php' => 'df9361a2ff65ebd1e2c300ec95ce35e03555f2ff632a818e49d1296946919d68',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleMaintenanceResource\\Pages\\CreateVehicleMaintenance.php' => '7a982d97049c9b21786a380298f52b0e19171a8a8801e44668a83defbd294eb2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleMaintenanceResource\\Pages\\EditVehicleMaintenance.php' => '7863d0bd5448e5cc0ca8c0d00b1eee7b7669b20931cdec5696882164ec75a0c9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleMaintenanceResource\\Pages\\ListVehicleMaintenances.php' => '128d1c4494fa4724ff0bcaef166f35f5a5be096516f6f210584d01ac22cde5a0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleResource.php' => '6811a56937998cbb7ca1c13484feb1572730720faf15cf3c4037806cd512ab95',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleResource\\Pages\\CreateVehicle.php' => '34baa25b14874d6365f6ce6c1621aa5d7b4fc2166d25f522b782eb46a9d91c63',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleResource\\Pages\\EditVehicle.php' => '74823e756ab2d88fbdd1aed4430f3134c2f89875659030d8c6f7e077c405f8de',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\VehicleResource\\Pages\\ListVehicles.php' => '705b8c09baa97c9a2fdca69fd9f1d3c522310039c01911b4256d38d949380b63',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowExecutionResource.php' => '9fe4a273a33222c66c6aeda8088a82291c6dbcee92e80063725f204b1fccf351',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowExecutionResource\\Pages\\ListWorkflowExecutions.php' => '6560210289bfce1c8681d90712a22024380968eea8fb0e20135168659005a434',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowExecutionResource\\Pages\\ViewWorkflowExecution.php' => '75dc6a38d3dde9367b573b0d3e059497c7642d262305b4b25a54311d58883bcb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowResource.php' => '29c2c71f13ba9966ebdb544045ce121933bfccebfde0b1f3dca89fc3ae845d5e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowResource\\Pages\\CreateWorkflow.php' => '665f62e4f2b2c59ace4dad7e2c2104a58f5727c2d53778b7f9929f2b15d8df16',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowResource\\Pages\\EditWorkflow.php' => 'e12d07d35723b8d8e42626d5509094f37d90a5fd1f494e6abb9249532d1d7f7e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Resources\\WorkflowResource\\Pages\\ListWorkflows.php' => 'a785c52881c5427e688c6d9d9c143f482bb6f5ea3c7d90b0215c695a490e49b4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Pages\\EstablishmentInfo.php' => '1221b5da83075e6bb780bb1e321ff45f7003bdd0da52f84b4cf3c9604f5fe4bd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Pages\\FeeConfiguration.php' => '3d1291348bddb9a4f06144fa51e6c0a88bc236e142fd2158c5f540df410b9507',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Pages\\ImportData.php' => '5cb73f03e2ec4a618cde29e25d7e6bca768f54e46f8898de7469f6e4428b3dfa',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Pages\\PDFSettings.php' => '041ae6f2b44e9af83eb1fe89028077d314a1ff1cbfff181c667c40f38ef3d4ef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Pages\\SchoolAdminDashboard.php' => '871afcb3c34c2278bda14846aa6e734d369157547ad50491a8425bdbf5753eb5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Pages\\SchoolSettings.php' => 'abd33b8d204f245d51142464eb1e8943a4936492df34922b998b5b75e1da8e8b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\ActiveAcademicYearWidget.php' => '7eda3d4ef5f6ae64078d19c14119413b3a852c79cb99a38a71ef2ded2a4ccd3f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\ClassesCountWidget.php' => '517d1346d9a624803f6c93d74e288b31899b9b6d83e3dff84c67174973ce3c6d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\ConfigAlertsWidget.php' => 'f3587e3209f9341b8e2abdafa7665c2d6eca536feb5973b603e5248742606e20',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\ConfigStatusWidget.php' => '9422774ca95b8d7f83aedd45389ece9d815651823a19401a03d9f83676f8458a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\ConfigurationAlertsWidget.php' => '5a9f7ab0e84f12328697faec93716812b26731d65a6a916734302c41aa3b1427',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\RecentEnrollmentsWidget.php' => '0e199d4bd17193c6084a031ddf6a30f17244d8e670b0bf009b3912f1dec82172',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\SetupStatusWidget.php' => 'ed4153386d4d64324bee76a71068438469e1801779065a875c42d62fae5eb3dc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\StaffCountWidget.php' => '87d759e4784141287a32058a6474a86dcbbf9a748e7d8948be673d87eadc11b2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\StudentsCountWidget.php' => 'de16972db7e65f9008bf662e90230c831e3a84c740284b53c4a568bad5c51908',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\SubjectsCountWidget.php' => 'fc758ed08be2d9aa27dbce14f8a08d3ac80433d20fda4777fa1cc55c4e8e00f9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SchoolAdmin\\Widgets\\TeachersCountWidget.php' => 'd5525108f80726b25a790f9563e292a177700730375137b30317668b38d46e80',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Secretary\\Pages\\SecretaryDashboard.php' => '60c1b27c04aff74e373c151c92fbf91a52dba20308e9d9b68a7448e5898ce0af',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Student\\Pages\\StudentDashboard.php' => '10a087725530fe26494391238fa85b5b7fcdf70f6a15d5f4c991be26a4ddd9ca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SuperAdmin\\Pages\\SuperAdminDashboard.php' => '7dc3965250f09b9c521647ca855cc5be47aa2b90550733d517b7594c1232dccc',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SuperAdmin\\Widgets\\RevenueChart.php' => '54843de9c482cf485de71b29915a71eb0e9835cd1e66f51c651b333468cf5c6b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SuperAdmin\\Widgets\\SchoolsChart.php' => '0280bd6c6d4739da467d22f07ecb13e226f29b649c1942b057a3c2c3407e0177',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SuperAdmin\\Widgets\\StatsOverview.php' => 'ca7b52a5cf2940221f766a7102fedd528058cf99127966896089f5b8fc231188',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\SuperAdmin\\Widgets\\SubscriptionStatusChart.php' => '0deb9baa31a9ad1796018289394739519f79dd9e50a371317cfc3cdb6df328c3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Filament\\Teacher\\Pages\\TeacherDashboard.php' => 'a97a10e43d2511585046bb84f81c7301327cb52cbbb26520f89bcc2a2607f307',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Controllers\\Api\\V1\\ApiClientController.php' => '0a9996db7a15baac94209ca33679685fcddcaaef00f10a04d88b44fb853d2ebd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Controllers\\Api\\V1\\ApiTokenController.php' => 'b45203fabbb984f8a88bbf8d78c78f47fc63f2ab23d34df808308c0c4a61993f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Controllers\\Api\\V1\\AuditLogController.php' => '9b081963b7b133bc099807ddb1023fc27f40a5e109df16dc0e5a9fe670fe0e0f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Controllers\\Api\\V1\\OAuthController.php' => 'd7fc454da71ccb5967571e85c4c8e4eaca268a2c264afc0dcb48fadd2cf10058',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Controllers\\Api\\V1\\WebhookController.php' => 'b07c4621f4b20e19bf55a12a39ed65f375cf9471243f9e21194108fea5134f88',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Controllers\\Controller.php' => '09e5cac5a69959ccf23d756cda697b993df937c106655d124ff27ca5fe24a705',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Middleware\\CheckApiRateLimit.php' => '947db68905599152b1551882cda300d504404b4d43f6e606e3b3793816fae4a0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Middleware\\InitializeTenancyForPanel.php' => '5d34eead301229bec2b1e04d507ae87ff0385ad93b4c307c2b321383f3d43ce9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Middleware\\LogApiRequest.php' => '3330e5e7586982288c96afe4cd2f3ba52786b889b12493e5d5cdeb8d09940db0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Middleware\\ValidateApiToken.php' => '45ce5b4412d1dfb00a31c040e1da6b392c53ab0f08d52a7458c51b7372a22b79',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Resources\\ApiAuditLogResource.php' => 'cbc0e02cfbf1ecaeb472182d0dd45f7018de5048f270865b405ab0fad1bc6eed',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Resources\\ApiClientResource.php' => 'afd584a0ac33efb29362e70a61b5494cdfd1579a84509277db54a503ac137cf7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Resources\\ApiTokenResource.php' => '2766823bb856fa730fb602b7930fb005ccca435278cf08a4b2883a696d4304e5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Resources\\WebhookDeliveryResource.php' => '1d279f83df9b42669f57074f4c679e13fab4ca4d33a1c430322635bf1f443637',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Http\\Resources\\WebhookResource.php' => 'b69ed7fc10d6647a7b32d3fa7c83b83b59a92f6f5b58aca0e8b85f390c6abec3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\CalculateKpis.php' => 'a09d3e49e2d3abbe6414e7c46ac75d47080b16f630141657dca892b10f8d030d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\CreateAnalyticsSnapshot.php' => '926e7a288e4ff42e19515d132ffd1e5d5da7e5b5261ae9eee7ab786a81d202eb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\DeliverWebhook.php' => '8024abf565eb9c99d0641ae589cc31ba47f7fa0e504b1a33dcefc7ec3bba61e4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\GenerateConversationSummary.php' => 'e48177bd876ab71bae4959e942e8760b063832f2e2ee79d6f23c45e1edeb255b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\GenerateReport.php' => 'ce6d69e7108d8804bb14b16f88450a1339c6e3944797878c93a6b8ac17ede165',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\IndexKnowledgeSource.php' => '961d9cfec62f3b392ebab112221d8be828cd7dbeaf3b15f20ff62e64b0ddf935',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\ProcessAiMessage.php' => 'ddaf5c9fcb292411b2515f41f9f1f52ffac563cfc9bbfc8613ba1027b1d9853b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\ProcessScheduledNotifications.php' => 'bad3d7f1b5d850b22239d08972f470856eaa78979b2149ca1593ed90a858e5ca',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\PruneExpiredTokens.php' => '92c44bf227cee3ca9359af0c739f1102adf2c42b3060e28df7243a65c5fc02b6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\RetryFailedNotification.php' => '7849fefc6ade3e7f2706c66cfc90e0b99221043bd85496445ae4399c5a7dbfcb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\RetryWebhookDelivery.php' => '5513a8a316f9315e9df39fa7b26382f5edd1704ea65da6dc391a2d7cf042a86f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\SendNotification.php' => '3f7cb6dedb01a32f181c013828fbfa913d02832155d57ff3e993d6bb8ad2fa9b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\System\\ProcessBackup.php' => '02f8d4d6742811f89857609c437a441fba7afe66a3e0d08be28314e0671c7e47',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\System\\ProcessHealthCheck.php' => '1e23c99faa5e16c51d4377f02f06daaa40ee180cad13384f89e2b810d2f73e7c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\Workflow\\ExecuteWorkflowNode.php' => 'fe595c4508820dbb90e7838a0529a4aaac053694cad50816e059c82b8159a441',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\Workflow\\ProcessScheduledWorkflow.php' => 'efac3faa2bfe1eb1abaf60d31ec7e933c69b113f68e2816fd47a275bc97595d1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Jobs\\Workflow\\TriggerAutomationRules.php' => '44bf0b259aa4542ef9f6206724ce2b6690a65b1b6e4409ad3eec916ab7226e82',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AcademicClass.php' => '9908ed1a629c4a54c7654ca25213fc53df8db44bb2e960a1be95339834e13803',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AcademicTerm.php' => '25c6a8d6ff31e125f309188fea1e1d8d1973ac7b31b72e045e3a088a97cef144',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AcademicYear.php' => 'f25fd6fa1882d22b925c223123467362a2b57450a62d99afd019966326e1d8f7',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\ActivityLog.php' => 'e35958d2f924d608ed34d23a33595befbdecb18c3de9312ac3a3dafae15ea389',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AiConversation.php' => 'bb110d5090a9947b3ab2c5fe523ca6ba16af5b91b36b9e315f4c4e508acaf2a5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AiKnowledgeSource.php' => '0542b8496413a1ebc119ae05fe836c13a09858f4c828a7e9ca6533db9b395914',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AiMessage.php' => '5c1924bdae3f0f43858d6644b2a371ea5602e6bd2edc89b69c979c13600db947',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AiPromptTemplate.php' => '90581ed857b7caf2a4d9b606b5b8f1a6792160c0766e9ab76ae6e484d346dcb3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AiProvider.php' => '72fe5864f692eb518b20cc93b9aa5da91e5ca981930fe003951fa06160f3a50d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AiUsageLog.php' => '403669c18b852a9f12c65497396bf8e9d04d989c637b25aa72c32a9c9687b4de',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AnalyticsSnapshot.php' => '3453d38791bd067e6a477d59fa66a9d56fd1d2c6348ae256064328e79daebc1d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Announcement.php' => 'd9ebc4901e9a2998f0898e50f682771acaa2d868f5bec79aa7936a4ef238d882',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\ApiAuditLog.php' => '78f38bce7bf2fa1d2c59f25087b0530344f2fd918c22bf27e9f3fbfa0a92a5ec',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\ApiClient.php' => '4484deffb9478a3c3516ac8a8c5e70ea3bc8547ff0ac91ccea60701584c2e60f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\ApiToken.php' => '8b4d34223601dd9d3b632d13ce35d7c4830a76087ae43fa615e203b5ef64014e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Assessment.php' => 'beb217274a36876343353e5bf02a426f9ec01f000fcb031e4372254ce3a2c05a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AssessmentResult.php' => 'b6b96d279c1a2bcb7396b1b497e7644e3416aaf5930a4bc322c3abe1203ec003',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AttendanceRecord.php' => 'baca17f8d5b503cb43a6588b3f9b01737bf6793c22b30d135202e6ae77324a82',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AuditLog.php' => 'a5f730abc964ec0ed73e4ea0b6a6f34ddfea286c1f2aad272b2b0a15fc183fff',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Author.php' => 'ebf15125a68eafe32932cd41332536d95cc373c9afd3298cdf1d6e460253789c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\AutomationRule.php' => '996ee959dd48c1922261aa7d229bef0545148655d220ec0b6b7e1ac7c3a93d44',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Backup.php' => '479c6d4b4ea1fb94b10478d456936717b5ef804c4480b76d650c3b3f852d0fc4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Book.php' => '767e6688201a7cd1f8673d781484dc88e54a429bdb6e98b834ef545b2f53b48f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\BookCategory.php' => 'e5d5f457b6c037461f9c921ae4bc367315568e2a91193989dd39544d8113c078',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\BookCopy.php' => 'a97f6ece37421b88e40a9df9e8e97027f2d8b722046a8e91b2a01245fe58d57f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\BookLoan.php' => 'f0594f71484c2afab7b7df73e3fd1ac3fa01b6f67e6a96389ce253a3fb25bd2b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Campus.php' => '4791c8d8c031310320502dda9a52371be15c6f2c0cb808cd7cd505f7ba38078f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\CentralDomain.php' => 'eabe77ee2dce42bde1117ce8ffb963c0f841cf8bf325b88cb25b62123bbcdfee',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Classroom.php' => '340efcbe0921f6cd783e1ebfa5cf0ebfabcb3e62ab3dead5b3ddff0c0664d3ce',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\CommunicationChannel.php' => '57ff84408cb9919d2e2d883eb39eef44a572c984a1d81298e3334c6e9676417b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Dashboard.php' => '5b53032a3dae2be05113f5738b7c2d48ba42deaef49739349d25ab16f2fd08a2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\DashboardWidget.php' => '946244f4f06ff3e30c2575ab7fef530adf76b626daf3eab225c43fa122f66543',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Department.php' => '691bc6e5845ea70afb7f50bb62023f82d372ae4295b1b941992912a0a1eccb4f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\DocumentTemplate.php' => '28d6d3ad6787a1d3daa344d5a1c032ea5a4c6a8baf253848db1bf341c16cf12b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Driver.php' => 'ec22b28e3925107e127d0ef66ebbc8245c3a4d195d9059b13ef185ab2b6eb5cd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Fine.php' => 'e0e429b0310904a9be3b2276d85c491cc73238b569dfa4d3abd3435e976526c2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\FuelLog.php' => '748890941b404e0d055aecdb1175716e6c279e1583c7feaa8e65ac983ea6ff81',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\GeneratedDocument.php' => '463b233b3eabe020e65091038cd2f43b13eb973d3ac275e1f8e1e02203ce0f89',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\GradeLevel.php' => 'da75d064d85eb8d606ef8580eda352421489aa36501d2dbb50d2e56316e60d0e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Guardian.php' => 'ace506dc6cb2e723f7776dedf29911f8a8cea359dd585618f17f33b33cd50179',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Incident.php' => '0e105033e2d369e93ac4702b0215a728e5fc9faf5d6f55a65c64a7a5953a6cef',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Kpi.php' => '6dd4a9d773acca5038a0b4fb92014b1c26a9ab47b98a8a6870626febb9fa5a04',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\MaintenanceWindow.php' => '6f3d76054a3df9c573b338d6f9081e3762be1707ff0be17edf6a6d82bc19b5d2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Message.php' => '19ec93dbfed03518d1be48f0ed187074919d6c5c6c8641b915e784f81fb17418',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\MessageRecipient.php' => '31c81601d65b247748c928116a3c12aa519772cfb8425eca023343481c463813',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Notification.php' => 'fff1faa5cca419da37c79a807789f82994d43a8f2a96c9d65268c3067bf4efac',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\NotificationChannel.php' => '7c9a9e6db9915ee1122e7f98c0f89cafd212b1e322b56bea763346c03e8fee0d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\NotificationDelivery.php' => '8b11088ef624ec7674b8b06bd175ce507a69f8d712d7083862ab0e6dd5d5eb2e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\NotificationTemplate.php' => '17ed1e12e16c7ddcf912b6b612a15d88662da6fb82cc8ceb2c283319562117fd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Payment.php' => 'e301bd0153ecfb2828f12a36bfb840c3c2549cc8a98181f9e13d79f8263a0b99',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Permission.php' => '360e5d84cb01452fa3d5fba5c50b92678bf260ac7aa2b290d0997115f9452dff',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Plan.php' => '79b33ab1269f91bf88b2bb6347dc04b537068ee04937f3e3541dd939ceb0cc84',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Plugin.php' => '3a7112b264fe8199eaf116dc06db2f2261649ec9716ece80dd029860090e5528',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\PluginSetting.php' => 'b1640a4ea3adea14b303b20909a9cec57f1a5a7246baa8bc435ab3824f40c52f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Program.php' => '193de8965664866eacb2d273a10ea543e77fb552920fbbe5857f906bc9387b64',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Publisher.php' => '178d5a41fc3dea891faec890b7a4a5fac91652318bcb93f0b3dbb00aa84252e6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Report.php' => '3b76ea43f028424b7354564088283d752a0027ab1a3f345838ef6a6d474e9997',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Reservation.php' => '00c2cbf75c21d25cc3e165f358edae906a06afc98dfa08b2a45a6fa525380e5f',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Role.php' => '531f94cc6566f09cf96cfc04306421bef97a42fbf8f7f1040b8b60fa4f3ced31',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Route.php' => 'de597234f1d62061cfae4654d693923860dc08352ff89fc545ebed9abfec95b3',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\RouteStop.php' => '164ed0e95532e9273f6d510c8da2712fecdad5a0a8402f1aec86171697844091',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\ScheduledTask.php' => '3aaf52b8b22051b57869efe189095fdcd929b859693c6f7933e8dadc50d43460',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Section.php' => '362faab5dfbbf7ae0495e6f6191b053e5439e51df3b2cd8125c635a15a9c455d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Student.php' => '7ad17ea953d1e120d554631d14fdba052644a0342c3115c9ef624370388273c6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\StudentEnrollment.php' => '36c3062e9e5a08d2ba2f0c4a1607bbcb9c68c76d0698a2e0c6fd46e010064389',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\StudentTransportAssignment.php' => 'e6cdf149756a61acaf25d19b04cde706489cbd16bc4c77f77d1268b827f4ef40',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Subject.php' => '288f16d051d80b23844832e2ca4be25720128ab722ad1e24b8919090372a4e70',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\SubjectCategory.php' => 'fd3dcca78d0d8694ff5051bee1a61eba5e1a736b93e19157e25abee8777314e9',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\SubjectCoefficient.php' => '7d5f67dd858ad4543fda78f5b6f228973e80493ccd49f3211d71ec06080c87d0',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Subscription.php' => 'dd59b20a9270437321a3037cec8199b8ed5d0bae0240fc5e2be33661419fd5c1',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\SystemHealth.php' => 'b5acf44eeb4da800a1f62ae15a54c3971c49bacae778859f53348fbdb015cc3a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\SystemSetting.php' => '7a2afac829c15cdd8db444a5b2992b9527a8acc3ccfd863fefa8a4fba0af3a4d',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Trip.php' => '2430fc1831db6af6f8dedd8cf1a59b183d6f6c181493e447a0964daebe724872',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\User.php' => 'e0a1e4b3777696d2dbbc3479576171f01f12bace19bb7eb3a40d499702734655',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Vehicle.php' => 'eb5ba6c4c0f4db800046aa15fef19e32419cc52266eeeb9d74054c6812a79ac2',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\VehicleMaintenance.php' => '893669c8a6843bb30046bf107e18313a9c16ae34016ac51170a80538814b065e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Webhook.php' => '571ac67467de879ce6dca9b10c64f68e0056c34e47f7fc5aafcecdb8bab57385',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\WebhookDelivery.php' => '8f67355a8de675c91341d3dfda1182f217d84f1f40570bcb3fc615b4597ee873',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\Workflow.php' => 'a9046b22832bfa9e4b7126ee2da20f4423eed9d149c3c8ccc00ca1356cef7b63',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\WorkflowExecution.php' => 'fde833f0b47155036a0586264497801b7f7568010bd467808af0cc643a9b99cd',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\WorkflowSchedule.php' => 'e067aaf9570f4f703318e75a149802aca44953b62806496766d6e64bab01275a',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\WorkflowTask.php' => '9bfbb4c169faed2f4eb356f9b02fa34fe9fd29ea0d3e9cf26e6b06a78972da88',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\WorkflowTrigger.php' => 'dddb5b712ddbfd3fd399488318abdaf9e69c7ff8a70ec05235a9741556495f3e',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Models\\WorkflowVersion.php' => '6ce21f4839cdcac54af368facc444cc148c7fbbbc7962cabc1b352e524b55863',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Policies\\TenantPolicy.php' => 'a1978ef3204aaeba23beec766f3a8c12f6867c71da8e4fe26be70363317d10ad',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Policies\\UserPolicy.php' => 'c48ce7340ded4740dc0d69de7ca9e0831e5dc31b9811995ebb4dea49c68b5fb6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\AppServiceProvider.php' => '3d6c99a6411dc3c0db410ec0eb3a70b288fbdf92c974fb3df9ef9e8578063213',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\EventServiceProvider.php' => 'df6f20626ef46941917c394213b6494a4f10fc80c7d76c907c9aa4689ccd4981',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\AccountantPanelProvider.php' => '794cfc518b4078fb13c770e1b47e2ae6c2e3bb46dafcb83c66abf64251feb413',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\DirectorPanelProvider.php' => '85f7a085f78fbbd10a331b478889322346e97f869164ac7c9bc34523e6c7f9a6',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\ParentPanelProvider.php' => '4555adf7aeb7fb362ac96d75af3aa98ebffc74b25d79e7a8627c5add84e12fcb',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\SchoolAdminPanelProvider.php' => 'afca0df29adbda193c5bba80ee7a536193422e87c0fbc0f6e5d60678d2c0c4f4',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\SecretaryPanelProvider.php' => '467d5e04edc4129000d1ad25e1b766c8f2f1adaca150578e01fb0afe9462ca23',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\StudentPanelProvider.php' => '9b3069c1fa27ecee22c062a8bbb50f7ce840ad0479709600168d610bf65c7031',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\SuperAdminPanelProvider.php' => 'd9fb9c07d4f499bea07a8e0a7ee4023c299dc24fb85a1331cb03e1664236e82c',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\Filament\\TeacherPanelProvider.php' => '5e9aa1be989ef1286d7fba0717215f11346ac5b94000b9b7a61372b71b7a9f54',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Providers\\TenancyServiceProvider.php' => '9704ab08f8a3f83e9bfb12f565e6a329cf602ee7ac45e0b1f3ad374dc322ead5',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Services\\DocumentGenerationService.php' => '98ff0f312f1ceaecd7dc7fee6b2324ba2fad5ca97d5fa0c69df6a251643c5665',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Services\\Workflows\\WorkflowRegistry.php' => '33ff1c79e1387ab0278fa54875166e15ddf50c92e9665a83ff26ccbff8f5f605',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Traits\\HasSchoolId.php' => 'a569d1c500d55b46f72b862b3a646b6ee284d682c5745632796873d69a49e33b',
    'C:\\xampp\\htdocs\\NexusSchool\\app\\Traits\\ScopedByTenant.php' => '4ba991143a1a00f121ff227d565b87d923b6674c7aa0406c7707ed60267ed19b',
  ),
  'composerLocks' => 
  array (
    'C:/xampp/htdocs/NexusSchool/composer.lock' => '48aedd582b244e33b6e047f3f274db78952e91d214e783a2024c1a45616529d9',
  ),
  'composerInstalled' => 
  array (
    'C:/xampp/htdocs/NexusSchool/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'anourvalar/eloquent-serialize' => 
        array (
          'pretty_version' => '1.3.10',
          'version' => '1.3.10.0',
          'reference' => '2be26f176b764a2d6f20118bfa4b125f71fa88f8',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../anourvalar/eloquent-serialize',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'blade-ui-kit/blade-heroicons' => 
        array (
          'pretty_version' => '2.7.0',
          'version' => '2.7.0.0',
          'reference' => '66fa8ba09dba12e0cdb410b8cb94f3b890eca440',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../blade-ui-kit/blade-heroicons',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'blade-ui-kit/blade-icons' => 
        array (
          'pretty_version' => '1.10.1',
          'version' => '1.10.1.0',
          'reference' => '6e072d021ea6249986c330b93293c33d0c4f0e34',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../blade-ui-kit/blade-icons',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'brianium/paratest' => 
        array (
          'pretty_version' => 'v7.8.5',
          'version' => '7.8.5.0',
          'reference' => '9b324c8fc319cf9728b581c7a90e1c8f6361c5e5',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../brianium/paratest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.14.8',
          'version' => '0.14.8.0',
          'reference' => '63422359a44b7f06cae63c3b429b59e8efcc0629',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../brick/math',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'carbonphp/carbon-doctrine-types' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../carbonphp/carbon-doctrine-types',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'chillerlan/php-qrcode' => 
        array (
          'pretty_version' => '5.0.5',
          'version' => '5.0.5.0',
          'reference' => '7b66282572fc14075c0507d74d9837dab25b38d6',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../chillerlan/php-qrcode',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'chillerlan/php-settings-container' => 
        array (
          'pretty_version' => '3.3.0',
          'version' => '3.3.0.0',
          'reference' => 'a0a487cbf5344f721eb504bf0f59bada40c381b7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../chillerlan/php-settings-container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'cordoval/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'danharrin/date-format-converter' => 
        array (
          'pretty_version' => 'v0.3.1',
          'version' => '0.3.1.0',
          'reference' => '7c31171bc981e48726729a5f3a05a2d2b63f0b1e',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../danharrin/date-format-converter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'danharrin/livewire-rate-limiting' => 
        array (
          'pretty_version' => 'v2.2.0',
          'version' => '2.2.0.0',
          'reference' => 'c03e649220089f6e5a52d422e24e3f98c73e456d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../danharrin/livewire-rate-limiting',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'davedevelopment/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dflydev/dot-access-data' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'reference' => 'a23a2bf4f31d3518f3ecb38660c95715dfead60f',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../dflydev/dot-access-data',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/deprecations' => 
        array (
          'pretty_version' => '1.1.6',
          'version' => '1.1.6.0',
          'reference' => 'd4fe3e6fd9bb9e72557a19674f44d8ac7db4c6ca',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../doctrine/deprecations',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => '6d6c96277ea252fc1304627204c3d5e6e15faa3b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/dompdf' => 
        array (
          'pretty_version' => 'v3.1.5',
          'version' => '3.1.5.0',
          'reference' => 'f11ead23a8a76d0ff9bbc6c7c8fd7e05ca328496',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../dompdf/dompdf',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/php-font-lib' => 
        array (
          'pretty_version' => '1.0.2',
          'version' => '1.0.2.0',
          'reference' => 'a6e9a688a2a80016ac080b97be73d3e10c444c9a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../dompdf/php-font-lib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/php-svg-lib' => 
        array (
          'pretty_version' => '1.0.2',
          'version' => '1.0.2.0',
          'reference' => '8259ffb930817e72b1ff1caef5d226501f3dfeb1',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../dompdf/php-svg-lib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'd61a8a9604ec1f8c3d150d09db6ce98b32675013',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../dragonmantank/cron-expression',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'egulias/email-validator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'reference' => 'd42c8731f0624ad6bdc8d3e5e9a4524f68801cfa',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../egulias/email-validator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'facade/ignition-contracts' => 
        array (
          'pretty_version' => '1.0.2',
          'version' => '1.0.2.0',
          'reference' => '3c921a1cdba35b68a7f0ccffc6dffc1995b18267',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../facade/ignition-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fakerphp/faker' => 
        array (
          'pretty_version' => 'v1.24.1',
          'version' => '1.24.1.0',
          'reference' => 'e0ee18eb1e6dc3cda3ce9fd97e5a0689a88a64b5',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../fakerphp/faker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fidry/cpu-core-counter' => 
        array (
          'pretty_version' => '1.3.0',
          'version' => '1.3.0.0',
          'reference' => 'db9508f7b1474469d9d3c53b86f817e344732678',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../fidry/cpu-core-counter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'filament/actions' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '1c479a47ee1612f7c05a7921b2491230aeed392c',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/actions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/filament' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => 'c54ec7692245787057c24f82d92d9fee7bc24941',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/filament',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/forms' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '7e6e4e52a63f4fc11edb253201bdd8cbc7d956eb',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/forms',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/infolists' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '01152bfc7d5d2b65a83eb1045a8f3625d4eaeae7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/infolists',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/notifications' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => 'a2b4564d9c7fcb5b8e904da40b4f6c2782934071',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/notifications',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/query-builder' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '401d0d61f5ead5d440f184857e1921675138fd5a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/query-builder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/schemas' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '036549f1b6cbf3d908e1b9fa42f5e221d1db3240',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/schemas',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/support' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '024caa553010c2617bb90114eb0e78883e33138b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/support',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/tables' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => 'bfe49781b2879e0c1acdfd4065d1c8499ee7ba1a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/tables',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filament/widgets' => 
        array (
          'pretty_version' => 'v5.6.8',
          'version' => '5.6.8.0',
          'reference' => '329c92e8560536fd6dd738b45eaf6ba063cd53ea',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filament/widgets',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'filp/whoops' => 
        array (
          'pretty_version' => '2.18.4',
          'version' => '2.18.4.0',
          'reference' => 'd2102955e48b9fd9ab24280a7ad12ed552752c4d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.4.0',
          'version' => '1.4.0.0',
          'reference' => '38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.4',
          'version' => '1.1.4.0',
          'reference' => 'e01f4a821471308ba86aa202fed6698b6b695e3b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.14.1',
          'version' => '7.14.1.0',
          'reference' => '6b1d2429a2c312474c523aa9017fba0c07b5f4a0',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.5.1',
          'version' => '2.5.1.0',
          'reference' => '9ad1e4fc607446a055b95870c7f668e93b5cff29',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.12.5',
          'version' => '2.12.5.0',
          'reference' => '9365d578a9fd1552ad6ca9c3cb530708526feb09',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.9',
          'version' => '1.0.9.0',
          'reference' => 'd7580af6d3f8384325d9cd3e99b21c3ed1848176',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../guzzlehttp/uri-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'hamcrest/hamcrest-php' => 
        array (
          'pretty_version' => 'v2.1.1',
          'version' => '2.1.1.0',
          'reference' => 'f8b1c0173b22fa6ec77a81fe63e5b01eba7e6487',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../hamcrest/hamcrest-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'iamcal/sql-parser' => 
        array (
          'pretty_version' => 'v0.7',
          'version' => '0.7.0.0',
          'reference' => '610392f38de49a44dab08dc1659960a29874c4b8',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../iamcal/sql-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'illuminate/auth' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/json-schema' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/reflection' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.63.0',
          ),
        ),
        'jean85/pretty-package-versions' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '4d7aa5dab42e2a76d99559706022885de0e18e1a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../jean85/pretty-package-versions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'kirschbaum-development/eloquent-power-joins' => 
        array (
          'pretty_version' => '4.3.2',
          'version' => '4.3.2.0',
          'reference' => '33c189bd51a510c1ceba67222395ead08a29863a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../kirschbaum-development/eloquent-power-joins',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'kodova/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'larastan/larastan' => 
        array (
          'pretty_version' => 'v3.10.0',
          'version' => '3.10.0.0',
          'reference' => '2970f83398154178a739609c244577267c7ee8eb',
          'type' => 'phpstan-extension',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v12.63.0',
          'version' => '12.63.0.0',
          'reference' => '7adfddbf4738f2e6cae5419b0e6bc46d4cccfbcf',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.7',
          'version' => '1.2.7.0',
          'reference' => '2f7d27dada8effc48b8c424445a69cca7007daaa',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.29.3',
          'version' => '1.29.3.0',
          'reference' => 'da1d1111a6aa2e082d2a388b194afe1ba0a05d14',
          'type' => 'project',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.21',
          'version' => '0.3.21.0',
          'reference' => '7753c65c281c2550c7c183f14e18062073b7d821',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/sail' => 
        array (
          'pretty_version' => 'v1.63.0',
          'version' => '1.63.0.0',
          'reference' => '51bbce3f803c1d386cabbb44e618c955a12ff5fc',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/sail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/sanctum' => 
        array (
          'pretty_version' => 'v4.3.2',
          'version' => '4.3.2.0',
          'reference' => '2a9bccc18e9907808e0018dd15fa643937886b1e',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/sanctum',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.13',
          'version' => '2.0.13.0',
          'reference' => 'b566ee0dd251f3c4078bed003a7ce015f5ea6dce',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v2.11.1',
          'version' => '2.11.1.0',
          'reference' => 'c9f80cc835649b5c1842898fb043f8cc098dd741',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.8.3',
          'version' => '2.8.3.0',
          'reference' => '1902f60f984235023acbe03db6ad614a37b3c3e7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/commonmark',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/config' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'reference' => '754b3604fb2984c71f4af4a9cbe7b57f346ec1f3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/csv' => 
        array (
          'pretty_version' => '9.28.0',
          'version' => '9.28.0.0',
          'reference' => '6582ace29ae09ba5b07049d40ea13eb19c8b5073',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/csv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.35.2',
          'version' => '3.35.2.0',
          'reference' => 'b277b5dc3d56650b68904117124e79c851e12376',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.31.0',
          'version' => '3.31.0.0',
          'reference' => '2f669db18a4c20c755c2bb7d3a7b0b2340488079',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/flysystem-local',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.17.0',
          'version' => '1.17.0.0',
          'reference' => 'f5f47eff7c48ed1003069a2ca67f316fb4021c76',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '08cf38e3924d4f56238125547b5720496fac8fd4',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-components' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '848ff9db2f0be06229d6034b7c2e33d41b4fd675',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/uri-components',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '85d5c77c5d6d3af6c54db4a78246364908f3c928',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'livewire/livewire' => 
        array (
          'pretty_version' => 'v4.3.3',
          'version' => '4.3.3.0',
          'reference' => '8021f2561865c4c297a3bfca37212a99034377e7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../livewire/livewire',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'masterminds/html5' => 
        array (
          'pretty_version' => '2.10.1',
          'version' => '2.10.1.0',
          'reference' => 'fd5018f6815fff903946d0564977b44ce8010e29',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../masterminds/html5',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mockery/mockery' => 
        array (
          'pretty_version' => '1.6.12',
          'version' => '1.6.12.0',
          'reference' => '1f4efdd7d3beafe9807b08156dfcb176d18f1699',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../mockery/mockery',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'monolog/monolog' => 
        array (
          'pretty_version' => '3.10.0',
          'version' => '3.10.0.0',
          'reference' => 'b321dd6749f0bf7189444158a3ce785cc16d69b0',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../monolog/monolog',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mtdowling/cron-expression' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '^1.0',
          ),
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.13.4',
          'version' => '1.13.4.0',
          'reference' => '07d290f0c47959fd5eed98c95ee5602db07e0b6a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nesbot/carbon' => 
        array (
          'pretty_version' => '3.13.1',
          'version' => '3.13.1.0',
          'reference' => '2937ad3d1d2c506fd2bc97d571438a95641f44e2',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/php-generator' => 
        array (
          'pretty_version' => 'v4.2.2',
          'version' => '4.2.2.0',
          'reference' => '0d7060926f5c3e8c488b9b9ced42d857f12a34b5',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nette/php-generator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.5',
          'version' => '1.3.5.0',
          'reference' => 'f0ab1a3cda782dbc5da270d28545236aa80c4002',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.1.4',
          'version' => '4.1.4.0',
          'reference' => '7da6c396d7ebe142bc857c20479d5e70a5e1aac7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.8.0',
          'version' => '5.8.0.0',
          'reference' => '044a6a392ff8ad0d61f14370a5fbbd0a0107152f',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.9.5',
          'version' => '8.9.5.0',
          'reference' => 'fb53eacd509a1d303858e2d20cfebf2d630254ec',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => '712a31b768f5daea284c2169a7d227031001b9a8',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../nunomaduro/termwind',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'openspout/openspout' => 
        array (
          'pretty_version' => 'v4.28.5',
          'version' => '4.28.5.0',
          'reference' => 'ab05a09fe6fce57c90338f83280648a9786ce36b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../openspout/openspout',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'paragonie/constant_time_encoding' => 
        array (
          'pretty_version' => 'v3.1.3',
          'version' => '3.1.3.0',
          'reference' => 'd5b01a39b3415c2cd581d3bd3a3575c1ebbd8e77',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../paragonie/constant_time_encoding',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'pestphp/pest' => 
        array (
          'pretty_version' => 'v3.8.7',
          'version' => '3.8.7.0',
          'reference' => 'f108313b52e8c28dc7121ce34303f817a3790202',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../pestphp/pest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin' => 
        array (
          'pretty_version' => 'v3.0.0',
          'version' => '3.0.0.0',
          'reference' => 'e79b26c65bc11c41093b10150c1341cc5cdbea83',
          'type' => 'composer-plugin',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../pestphp/pest-plugin',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-arch' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'reference' => 'db7bd9cb1612b223e16618d85475c6f63b9c8daa',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../pestphp/pest-plugin-arch',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-mutate' => 
        array (
          'pretty_version' => 'v3.0.5',
          'version' => '3.0.5.0',
          'reference' => 'e10dbdc98c9e2f3890095b4fe2144f63a5717e08',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../pestphp/pest-plugin-mutate',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'reference' => '54750ef60c58e43759730615a392c31c80e23176',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-common' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'reference' => '1d01c49d4ed62f25aa84a747ad35d5a16924662b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpdocumentor/reflection-common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-docblock' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => '7bae67520aa9f5ecc506d646810bd40d9da54582',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpdocumentor/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/type-resolver' => 
        array (
          'pretty_version' => '2.0.0',
          'version' => '2.0.0.0',
          'reference' => '327a05bbee54120d4786a0dc67aad30226ad4cf9',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpdocumentor/type-resolver',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.5',
          'version' => '1.9.5.0',
          'reference' => '75365b91986c2405cf5e1e012c5595cd487a98be',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpdoc-parser' => 
        array (
          'pretty_version' => '2.3.3',
          'version' => '2.3.3.0',
          'reference' => 'fb19eedd2bb67ff8cf7a5502ad329e701d6398a3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpstan/phpdoc-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.2.7',
          'version' => '2.2.7.0',
          'reference' => '692db47b9dddb0487934e5236e77d48594aef921',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '11.0.12',
          'version' => '11.0.12.0',
          'reference' => '2c1ed04922802c15e1de5d7447b4856de949cf56',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '5.1.1',
          'version' => '5.1.1.0',
          'reference' => '2f3a64888c814fc235386b7387dd5b5ed92ad903',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '5.0.1',
          'version' => '5.0.1.0',
          'reference' => 'c1ca3814734c07492b3d4c5f794f4b0995333da2',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '3e0404dc6b300e6bf56415467ebcb3fe4f33e964',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '7.0.1',
          'version' => '7.0.1.0',
          'reference' => '3b415def83fbcb41f991d9ebf16ae4ad8b7837b3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '11.5.56',
          'version' => '11.5.56.0',
          'reference' => '5f83edffa6967c3db468d48a695ec7bcb02e9256',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pragmarx/google2fa' => 
        array (
          'pretty_version' => 'v9.0.0',
          'version' => '9.0.0.0',
          'reference' => 'e6bc62dd6ae83acc475f57912e27466019a1f2cf',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../pragmarx/google2fa',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'pragmarx/google2fa-qrcode' => 
        array (
          'pretty_version' => 'v4.0.0',
          'version' => '4.0.0.0',
          'reference' => '16159f84fa0838c276f35d46de57fd90dfbb385c',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../pragmarx/google2fa-qrcode',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/container-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.1|2.0',
          ),
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-client' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/http-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-client-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-factory-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '2.0',
          'version' => '2.0.0.0',
          'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/log' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/log',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
            1 => '3.0.0',
          ),
        ),
        'psr/simple-cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psr/simple-cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
          ),
        ),
        'psy/psysh' => 
        array (
          'pretty_version' => 'v0.12.23',
          'version' => '0.12.23.0',
          'reference' => '4dcc0f08047d52bbde475eda481146fd8e27e1a4',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../psy/psysh',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ralouphie/getallheaders' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '120b605dfeb996808c31b6477290a714d356e822',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../ralouphie/getallheaders',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '344572933ad0181accbf4ba763e85a0306a8c5e2',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.9.3',
          'version' => '4.9.3.0',
          'reference' => '1df15849d00943a67d677dc9cfd80795f038c9f8',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.9.3',
          ),
        ),
        'ryangjchandler/blade-capture-directive' => 
        array (
          'pretty_version' => 'v1.1.1',
          'version' => '1.1.1.0',
          'reference' => '3f9e80b56ff60b78755ef320e3e16d88850101d6',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../ryangjchandler/blade-capture-directive',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'sabberworm/php-css-parser' => 
        array (
          'pretty_version' => 'v9.4.0',
          'version' => '9.4.0.0',
          'reference' => 'fd3bf9fb173e0df649bc4e3e0d088a1b2417c08f',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sabberworm/php-css-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'scrivo/highlight.php' => 
        array (
          'pretty_version' => 'v9.18.1.10',
          'version' => '9.18.1.10',
          'reference' => '850f4b44697a2552e892ffe71490ba2733c2fc6e',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../scrivo/highlight.php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => '15c5dd40dc4f38794d383bb95465193f5e0ae180',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/code-unit' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '54391c61e4af8078e5b276ab082b6d3c54c9ad64',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/code-unit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/code-unit-reverse-lookup' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '183a9b2632194febd219bb9246eee421dad8d45e',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/code-unit-reverse-lookup',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '6.3.3',
          'version' => '6.3.3.0',
          'reference' => '2c95e1e86cb8dd41beb8d502057d1081ccc8eca9',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'ee41d384ab1906c68852636b6de493846e13e5a0',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '6.0.2',
          'version' => '6.0.2.0',
          'reference' => 'b4ccd857127db5d41a5b676f24b51371d76d8544',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '7.2.1',
          'version' => '7.2.1.0',
          'reference' => 'a5c75038693ad2e8d4b6c15ba2403532647830c4',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '6.3.2',
          'version' => '6.3.2.0',
          'reference' => '70a298763b40b213ec087c51c739efcaa90bcd74',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '7.0.2',
          'version' => '7.0.2.0',
          'reference' => '3be331570a721f9a4b5917f4209773de17f747d7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => 'd36ad0d782e5756913e42ad87cb2890f4ffe467a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '6.0.1',
          'version' => '6.0.1.0',
          'reference' => 'f5b498e631a74204185071eb41f33f38d64608aa',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '6e1a43b411b2ad34146dee7524cb13a068bb35f9',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => 'f6458abbf32a6c8174f8f26261475dc133b3d9dc',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '5.1.3',
          'version' => '5.1.3.0',
          'reference' => 'f77d2d4e78738c98d9a68d2596fe5e8fa380f449',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '5.0.2',
          'version' => '5.0.2.0',
          'reference' => 'c687e3387b99f5b03b6caa64c74b63e2936ff874',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'spatie/invade' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => 'b920f6411d21df4e8610a138e2e87ae4957d7f63',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../spatie/invade',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-package-tools' => 
        array (
          'pretty_version' => '1.93.1',
          'version' => '1.93.1.0',
          'reference' => 'd5552849801f2642aea710557463234b59ef65eb',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../spatie/laravel-package-tools',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-pdf' => 
        array (
          'pretty_version' => '2.12.0',
          'version' => '2.12.0.0',
          'reference' => '9656389f566fc4e7435d1902b35c9c220e932b2c',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../spatie/laravel-pdf',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-permission' => 
        array (
          'pretty_version' => '6.25.0',
          'version' => '6.25.0.0',
          'reference' => 'd7d4cb0d58616722f1afc90e0484e4825155b9b3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../spatie/laravel-permission',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/once' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'spatie/shiki-php' => 
        array (
          'pretty_version' => '2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'b8b0ca32d3a82bc5c533e68ffab96c5d4ec1b9ba',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../spatie/shiki-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/temporary-directory' => 
        array (
          'pretty_version' => '2.4.0',
          'version' => '2.4.0.0',
          'reference' => '32cbb9645b28839cf4f476708e99a2c70e6802c9',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../spatie/temporary-directory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'staabm/side-effects-detector' => 
        array (
          'pretty_version' => '1.0.5',
          'version' => '1.0.5.0',
          'reference' => 'd8334211a140ce329c13726d4a715adbddd0a163',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../staabm/side-effects-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'stancl/jobpipeline' => 
        array (
          'pretty_version' => 'v1.9.0',
          'version' => '1.9.0.0',
          'reference' => 'be9481dff1e3d50d90ad4f618cdca5ec4d763ec1',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../stancl/jobpipeline',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'stancl/tenancy' => 
        array (
          'pretty_version' => 'v3.10.0',
          'version' => '3.10.0.0',
          'reference' => 'ab64f4599daf1152830e7d9a3c4af2d3e151b2e7',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../stancl/tenancy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'stancl/virtualcolumn' => 
        array (
          'pretty_version' => 'v1.5.0',
          'version' => '1.5.0.0',
          'reference' => '75718edcfeeb19abc1970f5395043f7d43cce5bc',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../stancl/virtualcolumn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/clock' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '674fa3b98e21531dd040e613479f5f6fa8f32111',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => '088ec6fe0ef6819cbc301174093b6bfa4ad26930',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => 'b75663ed96cf4756e28e3105476f220f92886cc4',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'f3202fa1b5097b0af062dc978b32ecf63404e31d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => '4e1a093b481f323e6e326451f9760c3868430673',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => '51fe3d170227be8d1772214b82ae506e15ed78ff',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'c7de7a00ffb67842132da02ea92988a39ccd9f4e',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0|3.0',
          ),
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => '13b38720174286f55d1761152b575a8d1436fc25',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/html-sanitizer' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => 'c328df69f5b6f44a0d031d757903d955bebb23b3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/html-sanitizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => '06db5ae1552177bf8572f8908839f12e3c06aed3',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => 'e99af79b1e776646eda0e1c23b7b45c184ff99be',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => 'f88ce03ae73e3edb5c176ce1f337709996e88495',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.4.13',
          'version' => '7.4.13.0',
          'reference' => 'a845722765c4f6b2ce88beaf4f4479975b186770',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '141046a8f9477948ff284fa65be2095baafb94f2',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.41.0',
          'version' => '1.41.0.0',
          'reference' => 'bb899c1db0aa8127dc3afe8cda4a67eb24915f8d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.38.1',
          'version' => '1.38.1.0',
          'reference' => 'dc21118016c039a66235cf93d96b435ffb282412',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-intl-idn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.38.0',
          'version' => '1.38.0.0',
          'reference' => '2d446c214bdbe5b71bde5011b060a05fece3ae6b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.38.2',
          'version' => '1.38.2.0',
          'reference' => 'd3d318bad5e7a1bfbd026009c8bfb8d8f99ae6b6',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => 'dfb55726c3a76ea3b6459fcfda1ec2d80a682411',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.38.2',
          'version' => '1.38.2.0',
          'reference' => '796a26abb75ce49f3a84433cd81bf1009d73d5f8',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php84' => 
        array (
          'pretty_version' => 'v1.38.1',
          'version' => '1.38.1.0',
          'reference' => 'f4e1dfaee5b74aba5964fe1fd4dfc7ba5e3085fa',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-php84',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php85' => 
        array (
          'pretty_version' => 'v1.41.0',
          'version' => '1.41.0.0',
          'reference' => '255fab485aaa1006ed411040c42aecd7b5302d7a',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-php85',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '26dfec253c4cf3e51b541b52ddf7e42cb0908e94',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.4.13',
          'version' => '7.4.13.0',
          'reference' => 'f5804be144caceb570f6747519999636b664f24c',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.4.13',
          'version' => '7.4.13.0',
          'reference' => '3a162171bb008e5e0f15dce6581373a4c0e8390d',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'c0a284bab1ed8aa0417e3d69250ab437739563a0',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => 'e394af32256bf9e7bf80849d95e589167c10097b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => 'a1af4dacb24eb7ef4f1ca71b94da8ddbce572281',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'ccb206b98faccc511ebae8e5fad50f2dc0b30621',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.3|3.0',
          ),
        ),
        'symfony/uid' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => '2676b524340abcfe4d6151ec698463cebafee439',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => '9a3a56a4a1e65a5cb4f8d13801fe8ab0a170e358',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/yaml' => 
        array (
          'pretty_version' => 'v7.4.13',
          'version' => '7.4.13.0',
          'reference' => 'a7ec3b1156faf8815db7683ec7c1e7338e6f977c',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../symfony/yaml',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'ta-tikoma/phpunit-architecture-test' => 
        array (
          'pretty_version' => '0.8.7',
          'version' => '0.8.7.0',
          'reference' => '1248f3f506ca9641d4f68cebcd538fa489754db8',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../ta-tikoma/phpunit-architecture-test',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'thecodingmachine/safe' => 
        array (
          'pretty_version' => 'v3.4.0',
          'version' => '3.4.0.0',
          'reference' => '705683a25bacf0d4860c7dea4d7947bfd09eea19',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../thecodingmachine/safe',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '1.3.1',
          'version' => '1.3.1.0',
          'reference' => 'b7489ce515e168639d17feec34b8847c326b0b3c',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'f0292ccf0ec75843d65027214426b6b163b48b41',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ueberdosis/tiptap-php' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '74bfb7be1c8c6102b240f3879b7f984a6ab87b97',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../ueberdosis/tiptap-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.4',
          'version' => '5.6.4.0',
          'reference' => '416df702837983f8d5ff48c9c3fee4f5f57b980b',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '8e1051fe39379367aecf014f41744ce7539a856f',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../voku/portable-ascii',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '2.4.1',
          'version' => '2.4.1.0',
          'reference' => '2ccb7c2e821038c03a3e6e1700c570c158c55f70',
          'type' => 'library',
          'install_path' => 'C:\\xampp\\htdocs\\NexusSchool\\vendor\\composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    'phar://C:\\xampp\\htdocs\\NexusSchool\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\Attribute85.php' => 'cb8b31e82c61ce197871c9e8a6f122256751f2ab606dd2be90846d4fa5f8933e',
    'phar://C:\\xampp\\htdocs\\NexusSchool\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionAttribute.php' => 'c0068e383717870a304781d462f7e2afe1c6f24e9133851852a2aca96b4fa26f',
    'phar://C:\\xampp\\htdocs\\NexusSchool\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionIntersectionType.php' => '65fe0a8bc6fe285d8ddc8798ab5b9299920af70db5ad74596bc08df823e7c5d9',
    'phar://C:\\xampp\\htdocs\\NexusSchool\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionUnionType.php' => '1e2fe940e4ba4e00d9ee6adb2af3ee1bf333e6f8afe61c61deb038886d293427',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'PDO',
    2 => 'Phar',
    3 => 'Reflection',
    4 => 'SPL',
    5 => 'SimpleXML',
    6 => 'bcmath',
    7 => 'bz2',
    8 => 'calendar',
    9 => 'ctype',
    10 => 'curl',
    11 => 'date',
    12 => 'dom',
    13 => 'exif',
    14 => 'fileinfo',
    15 => 'filter',
    16 => 'ftp',
    17 => 'gd',
    18 => 'gettext',
    19 => 'hash',
    20 => 'iconv',
    21 => 'intl',
    22 => 'json',
    23 => 'libxml',
    24 => 'mbstring',
    25 => 'mysqli',
    26 => 'mysqlnd',
    27 => 'openssl',
    28 => 'pcre',
    29 => 'pdo_mysql',
    30 => 'pdo_sqlite',
    31 => 'random',
    32 => 'readline',
    33 => 'session',
    34 => 'standard',
    35 => 'tokenizer',
    36 => 'xml',
    37 => 'xmlreader',
    38 => 'xmlwriter',
    39 => 'zip',
    40 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => '5',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined static method App\\Domains\\Student\\Infrastructure\\Models\\Student::create().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 44,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 44,
       'nodeType' => 'PhpParser\\Node\\Expr\\StaticCall',
       'identifier' => 'staticMethod.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Student\\Infrastructure\\Models\\Student>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 109,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 108,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Student\\Infrastructure\\Models\\Student>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 120,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 119,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 146,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 145,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 170,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 169,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined static method App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian::create().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 194,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 194,
       'nodeType' => 'PhpParser\\Node\\Expr\\StaticCall',
       'identifier' => 'staticMethod.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'line' => 195,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 195,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
       'line' => 21,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 21,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
       'line' => 29,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 29,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php',
       'line' => 21,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 21,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php',
       'line' => 24,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 24,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php',
       'line' => 21,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 21,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'line' => 20,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 20,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'line' => 24,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 24,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$transfer_from_school.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'line' => 32,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 32,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$transfer_to_school.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'line' => 35,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 35,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 26,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 26,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 38,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 38,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 38,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 37,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 84,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 84,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Student\\Infrastructure\\Models\\Student>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 84,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 83,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined static method App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian::where().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 100,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 100,
       'nodeType' => 'PhpParser\\Node\\Expr\\StaticCall',
       'identifier' => 'staticMethod.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined static method App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian::create().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 103,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 103,
       'nodeType' => 'PhpParser\\Node\\Expr\\StaticCall',
       'identifier' => 'staticMethod.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'line' => 104,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 104,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php',
       'line' => 21,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 21,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Student\\Infrastructure\\Models\\Student>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php',
       'line' => 21,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 20,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Student\\Infrastructure\\Models\\Student>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
       'line' => 92,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 92,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined static method App\\Models\\AuditLog::create().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
       'line' => 69,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 69,
       'nodeType' => 'PhpParser\\Node\\Expr\\StaticCall',
       'identifier' => 'staticMethod.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
       'line' => 70,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 70,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$first_name.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 120,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 120,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$last_name.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 120,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 120,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 188,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 188,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 193,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 193,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 198,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 198,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 203,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 203,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 208,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 208,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 213,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 213,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$status.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 218,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 218,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    9 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$preferred_name.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 223,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 223,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    10 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$last_name.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 224,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 224,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    11 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$full_name.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'line' => 225,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 225,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$uuid.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php',
       'line' => 18,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 18,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Models\\User::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 30,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 30,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 30,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 30,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Models\\User::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 44,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 44,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 44,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 44,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Models\\User::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 53,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 53,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 53,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 53,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Models\\User::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 92,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 92,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$school_id.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php (in context of class App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy)',
       'line' => 92,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
       'traitFilePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php',
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 92,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 78,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 77,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 78,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 78,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 85,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 84,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 85,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 85,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 92,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 91,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 92,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 92,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Builder<App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass>::forSchool().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 173,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 172,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 173,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 173,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 185,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 185,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    9 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 187,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 187,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    10 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 192,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 192,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    11 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 194,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 194,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    12 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 200,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 200,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    13 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 202,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 202,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    14 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 208,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 208,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    15 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 210,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 210,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    16 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'line' => 235,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 235,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->school_id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'line' => 29,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 29,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Contracts\\Auth\\Factory::user().',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
       'line' => 29,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 29,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Domains\\Student\\Infrastructure\\Models\\Student::$guardians.',
       'file' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
       'line' => 34,
       'canBeIgnored' => true,
       'filePath' => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 34,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$student->isArchived():22',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
        1 => 'dispatch',
        2 => 40,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$year->isActive():153',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => '$admission->lt($yearStart):162',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => '$admission->gt($yearEnd):162',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
        1 => 'ensureStudentNumber',
        2 => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\dtos\\createstudentdata' . "\0" . '__construct',
          1 => 'm' . "\0" . 'app\\domains\\student\\application\\services\\studentnumbergenerator' . "\0" . 'generate',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
        1 => 'isPrimaryGuardian',
        2 => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureNewCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        1 => 97,
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        1 => 101,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
        1 => 'dispatch',
        2 => 48,
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        1 => 'from',
        2 => 136,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$student->isGraduated():18',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isActive():26',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
        1 => 'dispatch',
        2 => 41,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isSuspended():18',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isWithdrawn():18',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isInactive():18',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
        1 => 'dispatch',
        2 => 34,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isArchived():21',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
        1 => 'dispatch',
        2 => 39,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isActive():18',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
        1 => 'dispatch',
        2 => 35,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$student->isImmutable():19',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$student->isGraduated():23',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => '$data->transferStatus->isActive():30',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
        1 => 'dispatch',
        2 => 46,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$student->isImmutable():25',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Actions\\UpdateStudentAction',
        1 => 'isPrimaryGuardian',
        2 => 'App\\Domains\\Student\\Application\\Actions\\UpdateStudentAction',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureNewCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        1 => 72,
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        1 => 76,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        1 => 'from',
        2 => 33,
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
        1 => 'dispatch',
        2 => 63,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isActive():18',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$student->isSuspended():18',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
        1 => 'dispatch',
        2 => 34,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
        1 => 'toArray',
        2 => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\TransferStudentData.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
        1 => 'toArray',
        2 => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
        1 => 'toArray',
        2 => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        1 => 'format',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'register',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\createstudentaction' . "\0" . 'execute',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'update',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\updatestudentaction' . "\0" . 'execute',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'transfer',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\transferstudentaction' . "\0" . 'execute',
        ),
      ),
      3 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'suspend',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\suspendstudentaction' . "\0" . 'execute',
        ),
      ),
      4 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'graduate',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\graduatestudentaction' . "\0" . 'execute',
        ),
      ),
      5 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'withdraw',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\withdrawstudentaction' . "\0" . 'execute',
        ),
      ),
      6 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'reactivate',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\reactivatestudentaction' . "\0" . 'execute',
        ),
      ),
      7 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'archive',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\archivestudentaction' . "\0" . 'execute',
        ),
      ),
      8 => 
      array (
        0 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        1 => 'restore',
        2 => 'App\\Domains\\Student\\Application\\Services\\StudentService',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\application\\actions\\restorestudentaction' . "\0" . 'execute',
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\Gender.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
        1 => 'label',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\StudentStatus.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        1 => 'label',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        1 => 'color',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        1 => 'isImmutable',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        3 => 
        array (
        ),
      ),
      3 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        1 => 'isActive',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\TransferStatus.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        1 => 'label',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        1 => 'color',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        1 => 'isActive',
        2 => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentArchived.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentCreated.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentGraduated.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentReactivated.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentRestored.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentSuspended.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentTransferred.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentUpdated.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentWithdrawn.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        1 => 'missingYear',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        1 => 'inactiveYear',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        1 => 'classMismatch',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
        1 => 'studentNumber',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
        1 => 'email',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
        1 => 'message',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException',
        1 => 'for',
        2 => 'App\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\RuleUnique.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
        1 => 'studentNumber',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\ruleunique' . "\0" . 'column',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
        1 => 'email',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\ruleunique' . "\0" . 'column',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
        1 => 'admissionNumber',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\ruleunique' . "\0" . 'column',
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        1 => 'create',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\studentrules' . "\0" . 'uniqueemail',
          1 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\studentrules' . "\0" . 'uniquenumber',
          2 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\studentrules' . "\0" . 'uniqueadmissionnumber',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        1 => 'update',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\studentrules' . "\0" . 'uniqueemail',
          1 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\studentrules' . "\0" . 'uniquenumber',
          2 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\studentrules' . "\0" . 'uniqueadmissionnumber',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        1 => 'uniqueNumber',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\ruleunique' . "\0" . 'studentnumber',
        ),
      ),
      3 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        1 => 'uniqueEmail',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\ruleunique' . "\0" . 'email',
        ),
      ),
      4 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        1 => 'uniqueAdmissionNumber',
        2 => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\rules\\ruleunique' . "\0" . 'admissionnumber',
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        1 => 15,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Address.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        1 => 'nullable',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        1 => 'isComplete',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        1 => 'formatted',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference',
        1 => 'value',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\BirthDate.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => 'is_string($date):18',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        1 => 'age',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        3 => 
        array (
          0 => 'm' . "\0" . 'datetimeimmutable' . "\0" . '__construct',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        1 => 'toDateString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'filter_var($value, FILTER_VALIDATE_EMAIL):27',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        1 => 'value',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureNewCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        1 => 26,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'in_array($relationship, self::RELATIONSHIPS, true):47',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        1 => 'display',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        1 => 'toArray',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureNewCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        1 => 55,
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
        1 => 59,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Nationality.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Nationality',
        1 => 'value',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Nationality',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Nationality',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Nationality',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'preg_match(\'/^[+]?[0-9][0-9\\s().-]{4,30}$/\', $value):21',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        1 => 'value',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Photo.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        1 => 'isSet',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        1 => 'value',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentName.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
        1 => 'fullName',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
        1 => 'displayName',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\valueobjects\\studentname' . "\0" . 'fullname',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\domain\\valueobjects\\studentname' . "\0" . 'fullname',
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
        1 => 'value',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
        1 => 'equals',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
        1 => '__toString',
        2 => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'newFactory',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
          0 => 'm' . "\0" . 'illuminate\\database\\eloquent\\factories\\factory' . "\0" . '__construct',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'casts',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'getFullNameAttribute',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      3 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'isActive',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      4 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'isArchived',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      5 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'isGraduated',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      6 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'isSuspended',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      7 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'isWithdrawn',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
      8 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        1 => 'isInactive',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
      1 => 
      array (
        0 => 'App\\Traits\\HasSchoolId',
      ),
      2 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        1 => 'casts',
        2 => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
      1 => 
      array (
        0 => 'App\\Traits\\HasSchoolId',
      ),
      2 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => 'in_array(\'uuid\', $model->getFillable(), true):18',
        3 => NULL,
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):21',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->permissionGroup():21',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.view\'):21',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanOrConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\') || $user->hasPermissionTo($this->permissionGroup() . \'.view\'):21',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):26',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:30',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->permissionGroup():30',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.view\'):30',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:30',
        3 => NULL,
      ),
      9 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup() . \'.view\'):30',
        3 => NULL,
      ),
      10 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):35',
        3 => NULL,
      ),
      11 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->permissionGroup():35',
        3 => NULL,
      ),
      12 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.create\'):35',
        3 => NULL,
      ),
      13 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanOrConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\') || $user->hasPermissionTo($this->permissionGroup() . \'.create\'):35',
        3 => NULL,
      ),
      14 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):40',
        3 => NULL,
      ),
      15 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:44',
        3 => NULL,
      ),
      16 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->permissionGroup():44',
        3 => NULL,
      ),
      17 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.update\'):44',
        3 => NULL,
      ),
      18 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:44',
        3 => NULL,
      ),
      19 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup() . \'.update\'):44',
        3 => NULL,
      ),
      20 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):49',
        3 => NULL,
      ),
      21 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:53',
        3 => NULL,
      ),
      22 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->permissionGroup():53',
        3 => NULL,
      ),
      23 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.delete\'):53',
        3 => NULL,
      ),
      24 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:53',
        3 => NULL,
      ),
      25 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup() . \'.delete\'):53',
        3 => NULL,
      ),
      26 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->authorizeLifecycle($user, $model, \'restore\'):58',
        3 => NULL,
      ),
      27 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->authorizeLifecycle($user, $model, \'transfer\'):68',
        3 => NULL,
      ),
      28 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->authorizeLifecycle($user, $model, \'suspend\'):73',
        3 => NULL,
      ),
      29 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->authorizeLifecycle($user, $model, \'graduate\'):78',
        3 => NULL,
      ),
      30 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->authorizeLifecycle($user, $model, \'archive\'):83',
        3 => NULL,
      ),
      31 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):88',
        3 => NULL,
      ),
      32 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:92',
        3 => NULL,
      ),
      33 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$this->permissionGroup():92',
        3 => NULL,
      ),
      34 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.\' . $action):92',
        3 => NULL,
      ),
      35 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id:92',
        3 => NULL,
      ),
      36 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->school_id === $model->school_id && $user->hasPermissionTo($this->permissionGroup() . \'.\' . $action):92',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanOrConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):21',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanOrConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.view\'):21',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):26',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.view\'):30',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanOrConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):35',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanOrConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.create\'):35',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):40',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.update\'):44',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):49',
        3 => NULL,
      ),
      9 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.delete\'):53',
        3 => NULL,
      ),
      10 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasRole(\'super_admin\'):88',
        3 => NULL,
      ),
      11 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
        2 => '$user->hasPermissionTo($this->permissionGroup() . \'.\' . $action):92',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'restore',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\infrastructure\\policies\\studentpolicy' . "\0" . 'authorizelifecycle',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'forceDelete',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'transfer',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\infrastructure\\policies\\studentpolicy' . "\0" . 'authorizelifecycle',
        ),
      ),
      3 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'suspend',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\infrastructure\\policies\\studentpolicy' . "\0" . 'authorizelifecycle',
        ),
      ),
      4 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'graduate',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\infrastructure\\policies\\studentpolicy' . "\0" . 'authorizelifecycle',
        ),
      ),
      5 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'archive',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\domains\\student\\infrastructure\\policies\\studentpolicy' . "\0" . 'authorizelifecycle',
        ),
      ),
      6 => 
      array (
        0 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        1 => 'permissionGroup',
        2 => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => '$record->isActive():185',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => '$record->isActive():192',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$record->isArchived():200',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => '$record->isArchived():208',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
        1 => 'getRelations',
        2 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
        1 => 'getPages',
        2 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
        3 => 
        array (
          0 => 'm' . "\0" . 'filament\\resources\\pages\\page' . "\0" . 'route',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
        1 => 'canDeleteAny',
        2 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Traits\\ScopedByTenant',
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        1 => 'buildGuardians',
        2 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
        3 => 
        array (
          0 => 'f' . "\0" . 'array_map',
          1 => 'm' . "\0" . 'app\\domains\\student\\domain\\valueobjects\\guardianreference' . "\0" . 'fromnullableguardian',
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent',
        1 => 'buildGuardians',
        2 => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent',
        3 => 
        array (
          0 => 'f' . "\0" . 'array_map',
          1 => 'm' . "\0" . 'app\\domains\\student\\domain\\valueobjects\\guardianreference' . "\0" . 'fromnullableguardian',
        ),
      ),
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 57,
      ),
    ),
  ),
); },
	'dependencies' => array (
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php' => 
  array (
    'fileHash' => 'b38da5e1a6fc14781234a52391afbf18f2d15d193ddc7b74df0a0c3e9904b295',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php' => 
  array (
    'fileHash' => '5bc12c51f21cde0c3ae6e6b7f5e45b1d7f71539f016c203fb00f48e1d2ef0eb5',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php' => 
  array (
    'fileHash' => '41c4557cc80c2c25efd45e60ad25f90a27eff1e5b3562d43e283501c721200d4',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php' => 
  array (
    'fileHash' => '9d9281e74ab211d446299a1034c30a9408ba5f78378049a8c7623b9d2936bfcc',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php' => 
  array (
    'fileHash' => 'a603299dd0fbcf9ba9f2a86b0fcefb52b6ab933db9e09d6a67ebad813bad6e22',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php' => 
  array (
    'fileHash' => 'c151a2c3250565bef80457095b21447cc2f998267c7ee02cb6692c7c388a2a27',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php' => 
  array (
    'fileHash' => '89afa84cae33cd87b7a6dd0d8f35a234026b14efd1bb2afc2bbd886782942f5e',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php' => 
  array (
    'fileHash' => '008e639906cd90e5aaab0d896365d1adc04644740c236ccd9319ae0cb0ad0996',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php' => 
  array (
    'fileHash' => 'd9b3905eba25d43aad8ea68ddfac7a24c7a220abea605d8507c8e4e3872d8621',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php' => 
  array (
    'fileHash' => '28e849a64e6169ec9cffbcb5aa1c95b173b71aa949992e8bc7f7867063b11673',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\TransferStudentData.php' => 
  array (
    'fileHash' => '8c974ad7428b6831fd3fa1e902c0097beee796c8e05de7aa20277ed9d266e104',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php' => 
  array (
    'fileHash' => '3304f492d424b942955200e1958ba70c79ea341b56311d9c661604867ca43b92',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php' => 
  array (
    'fileHash' => '3425bf041253013dbac634cd9d276b73c1c1ab536d856d07b0e6ca2c9a86c209',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php' => 
  array (
    'fileHash' => '99882033154338feec15e4d74615cb71361f6c379fd0452aee1e42ad482303df',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\Gender.php' => 
  array (
    'fileHash' => 'ad8048e93df737040268841c974fe1eb701e6fc43f7886c7500bcb33b4cc3b37',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php',
      4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
      5 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\StudentStatus.php' => 
  array (
    'fileHash' => '03e339fbf94fc809987f1bfbe516e90d8a9d629cbb36197f6bcabd1f0932b972',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php',
      4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php',
      5 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php',
      6 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php',
      7 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php',
      8 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php',
      9 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
      10 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\TransferStatus.php' => 
  array (
    'fileHash' => 'd42e8a680e7c0daaffd211623418f80f9b750261e7cc2d70613191e0af6a402f',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\TransferStudentData.php',
      4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php',
      5 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentArchived.php' => 
  array (
    'fileHash' => '1a0995c781228aac5c4f867c072ace03c1e04d968bfa672bf1c67a71c6a8dc57',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentCreated.php' => 
  array (
    'fileHash' => 'b3b89b25456ef2132fdd6a4a190335382b8b2d19579f5d57fe164ef19c839d02',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentGraduated.php' => 
  array (
    'fileHash' => '1eb9084081e9c627c804c4df5bc5b90b0d2200c61a505c15d294d509d8dbbe4d',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentReactivated.php' => 
  array (
    'fileHash' => '2579b5529b71162c081861c2ea78dc55a0458f6104bba527ade419d9c3425fc4',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentRestored.php' => 
  array (
    'fileHash' => '4415b90c1eb51615b03f9692619cd2f21d69cefc0dabc34f35e2634960d6bec3',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentSuspended.php' => 
  array (
    'fileHash' => '79342c6bd268f7e44bb9434b4303439560d7858990fa22f92b4a5e3b2481571d',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentTransferred.php' => 
  array (
    'fileHash' => 'f2c7a9097e214c6cdf9d22086194f5513c5698101ec1aa91ca16005741d19382',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentUpdated.php' => 
  array (
    'fileHash' => '3973e985d16a5538a6a05dd2a763044bcdee0e082c49ca15ea8b786819dcdc99',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentWithdrawn.php' => 
  array (
    'fileHash' => '2799c93e24c506852e052c434679216725306a8c4204a5bc44f660568b08d09c',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException.php' => 
  array (
    'fileHash' => '5fae31856bcc9144eba40aad186fc9eb044471bdc3f4a66bd7ff3fb5b18048e6',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException.php' => 
  array (
    'fileHash' => 'c6dee2415e4d68f709529e8aaa66c2549b826a44be2153953f95b01b5ae9ac61',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException.php' => 
  array (
    'fileHash' => '98baa63b3a696824f2fb364c927204bb1e6fd3441b4d3e59e99b49da4e6c6cfa',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php',
      4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php',
      5 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
      6 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      7 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\RuleUnique.php' => 
  array (
    'fileHash' => 'ae4eaeac391432ea94ab2674bb8944f71f28cdbd5c029c6539de82af13dbe1b3',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php' => 
  array (
    'fileHash' => '09a9d5dc3a89e8af6963f1a82f0ffbc4e45b88962e6623499352af6d90141d6c',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php' => 
  array (
    'fileHash' => '3c0ad02eb5ccbbc836e5bfc1232077cd8935c700feb6196a5f3f5c5af64167f5',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Address.php' => 
  array (
    'fileHash' => 'e98a4f3ffa77046cb0792f1f705e6b998853018223b6a0ae256ebfa7fe4c943f',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference.php' => 
  array (
    'fileHash' => '3131e237f7a466c88df5e68843be7e61fe87639b76c1f190cdc01f32d3ad00e2',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\BirthDate.php' => 
  array (
    'fileHash' => '05bc3941cb70b8058f931081840473691f6b43b068460e0b89bccf5965a3058d',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress.php' => 
  array (
    'fileHash' => 'ea6728da301259be02eaf165229205f577ec21f891d189c1381822632397b536',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact.php' => 
  array (
    'fileHash' => '38ebabecd4e4015a8cb5acda503c6a47ed25336ea0f5620b6e52dd59e18bbef5',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference.php' => 
  array (
    'fileHash' => '03f438ec7e31750c8dc8d848240c2fa7291fb2dc9e1a321936bc3bd54de92aa3',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php',
      4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
      5 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Nationality.php' => 
  array (
    'fileHash' => '06f9ef2c906561c5f16a86a4b9dc841ae38f050efb9207d8e7836516ee934a1f',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber.php' => 
  array (
    'fileHash' => 'b570c255e66b9061334be672d87a0054d0c41443e8031283c1113eb836312529',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Photo.php' => 
  array (
    'fileHash' => '05e83ee56748c329dcc099d3f11af2c7c4e705cf188344b21a8196d4a974c3a6',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentName.php' => 
  array (
    'fileHash' => '944242b7672d498526168a303f4f943499907057a830b3bf6d4d93e4cbd7f085',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber.php' => 
  array (
    'fileHash' => '9c791a60034f3a7820dfc4bcd2b4d8bfcc36afbf8bb061e314057205c307293a',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php' => 
  array (
    'fileHash' => 'd79b2492ee57b2d9cc6e81410f539ff4663dbb3c43e33a83b88446cfdbd8f7bd',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php' => 
  array (
    'fileHash' => 'cb49135cad998bf4b7bda54ea6a1f99000f725e737cf3b132f9605306d6e1eca',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php',
      4 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php',
      5 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php',
      6 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php',
      7 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      8 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php',
      9 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php',
      10 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php',
      11 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentArchived.php',
      12 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentCreated.php',
      13 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentGraduated.php',
      14 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentReactivated.php',
      15 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentRestored.php',
      16 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentSuspended.php',
      17 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentTransferred.php',
      18 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentUpdated.php',
      19 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentWithdrawn.php',
      20 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php',
      21 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian.php',
      22 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
      23 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
      24 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
      25 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian.php' => 
  array (
    'fileHash' => '73fa00c8911503a78bea03c5b7030cc00cb13adadd30f4bc5dccad81ad653ae6',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php',
      3 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php' => 
  array (
    'fileHash' => '604580196ee0c027d9118d175b23a021158b67b5c0496b74caccc12774526771',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php' => 
  array (
    'fileHash' => '0754a3e160e2e57f4085c8b951ef653aa3719c7efb72d169ec552b4d1ef14992',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php' => 
  array (
    'fileHash' => 'e4a1bc498ded2c6b96984c7a70a0ea8ce7581b78d4e26d01fa2a17e23a1dd4b4',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php',
      1 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php',
      2 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\ListStudents.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php' => 
  array (
    'fileHash' => '44872b2abf1c414f48ffed9d8200d3137f945db9c311dcc9948993f3d963b69c',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php' => 
  array (
    'fileHash' => '4eb85258873a7891d093f652ce2b33abdd72840338baf8320e755eb1ead0d0c1',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\ListStudents.php' => 
  array (
    'fileHash' => '37a905eebd3c7643a0cec604835dc59c297e6acac09b9340be3b078f9223df4a',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\RelationManagers\\GuardiansRelationManager.php' => 
  array (
    'fileHash' => '9a2e4e9cda2ffc1724a3319d0ee76f1f2a4a9f16646faa1d909a2324c9a80a22',
    'dependentFiles' => 
    array (
      0 => 'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php',
    ),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php' => 
  array (
    'fileHash' => '1bc1baef6d7b5077354acf96bc3b3bd55f76a9c977be414ea23dbf7da1515f2b',
    'dependentFiles' => 
    array (
    ),
  ),
),
	'packageDependencies' => array (
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php' => 
  array (
    0 => 'filament/filament',
    1 => 'laravel/framework',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\StudentStatus.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentArchived.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentGraduated.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentUpdated.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\RuleUnique.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\BirthDate.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentName.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'stancl/tenancy',
    2 => 'stancl/virtualcolumn',
    3 => 'nesbot/carbon',
    4 => 'filament/filament',
    5 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'symfony/uid',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'filament/schemas',
    3 => 'filament/support',
    4 => 'filament/forms',
    5 => 'stancl/tenancy',
    6 => 'filament/tables',
    7 => 'filament/actions',
    8 => 'danharrin/livewire-rate-limiting',
    9 => 'livewire/livewire',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php' => 
  array (
    0 => 'filament/filament',
    1 => 'laravel/framework',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\TransferStudentData.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\Gender.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\TransferStatus.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentReactivated.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentRestored.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentSuspended.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Nationality.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Photo.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php' => 
  array (
    0 => 'filament/actions',
    1 => 'filament/schemas',
    2 => 'filament/filament',
    3 => 'danharrin/livewire-rate-limiting',
    4 => 'livewire/livewire',
    5 => 'laravel/framework',
    6 => 'stancl/tenancy',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php' => 
  array (
    0 => 'filament/actions',
    1 => 'filament/schemas',
    2 => 'filament/filament',
    3 => 'danharrin/livewire-rate-limiting',
    4 => 'livewire/livewire',
    5 => 'laravel/framework',
    6 => 'filament/support',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\ListStudents.php' => 
  array (
    0 => 'filament/tables',
    1 => 'filament/actions',
    2 => 'filament/schemas',
    3 => 'filament/filament',
    4 => 'livewire/livewire',
    5 => 'danharrin/livewire-rate-limiting',
    6 => 'laravel/framework',
    7 => 'filament/support',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
    3 => 'nesbot/carbon',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentCreated.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentTransferred.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentWithdrawn.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'filament/filament',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Address.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber.php' => 
  array (
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php' => 
  array (
    0 => 'filament/filament',
    1 => 'laravel/framework',
    2 => 'spatie/laravel-permission',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\RelationManagers\\GuardiansRelationManager.php' => 
  array (
    0 => 'filament/actions',
    1 => 'filament/schemas',
    2 => 'filament/tables',
    3 => 'filament/filament',
    4 => 'filament/support',
    5 => 'danharrin/livewire-rate-limiting',
    6 => 'livewire/livewire',
    7 => 'laravel/framework',
    8 => 'filament/forms',
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'psr/container',
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Archives a student record (soft delete + terminal status). Archived records
 * are immutable and excluded from active queries.
 */',
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'invalidstudentstateexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'user' => 'App\\Models\\User',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\CreateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'numberGenerator',
               'type' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\GraduateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\GraduateStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\RestoreStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\RestoreStudentAction',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Restores a previously archived student back to an active state.
 */',
         'namespace' => 'App\\Domains\\Student\\Application\\Actions',
         'uses' => 
        array (
          'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'invalidstudentstateexception' => 'App\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'user' => 'App\\Models\\User',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\SuspendStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\SuspendStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'until',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\TransferStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\TransferStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\UpdateStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\UpdateStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'execute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\CreateStudentData.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  list<GuardianReference>  $guardians
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'firstName',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'lastName',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'studentNumber',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'middleName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            5 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'preferredName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            6 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'gender',
               'type' => '?App\\Domains\\Student\\Domain\\Enums\\Gender',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            7 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'birthDate',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            8 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nationality',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            9 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'email',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            10 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'phone',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            11 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'admissionDate',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            12 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'admissionNumber',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            13 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'academicCalendarYearId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            14 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolClassId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            15 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'classSectionId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            16 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nationalId',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            17 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'birthCertificateReference',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            18 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'photoPath',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            19 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressStreet',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            20 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressCity',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            21 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressState',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            22 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressPostalCode',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            23 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressCountry',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            24 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'emergencyContact',
               'type' => '?App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            25 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'guardians',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            26 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'medicalNotes',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            27 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'studentStatus',
               'type' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            28 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferStatus',
               'type' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            29 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferFromSchool',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array<string, mixed>  $data
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'withGuardians',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  list<GuardianReference>  $guardians
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'guardians',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\TransferStudentData.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferStatus',
               'type' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferFromSchool',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferToSchool',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferNote',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolClassId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array<string, mixed>  $data
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\DTOs\\UpdateStudentData.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Represents a mutable update to a student\'s demographic, contact and academic
 * assignment. Fields that are null keep their current persisted value.
 */',
         'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
         'uses' => 
        array (
          'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
          'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
          'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  list<GuardianReference>  $guardians
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'firstName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'lastName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'middleName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'preferredName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'gender',
               'type' => '?App\\Domains\\Student\\Domain\\Enums\\Gender',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            5 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'birthDate',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            6 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nationality',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            7 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'email',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            8 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'phone',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            9 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'admissionDate',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            10 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'academicCalendarYearId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            11 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolClassId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            12 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'classSectionId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            13 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nationalId',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            14 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'birthCertificateReference',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            15 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'photoPath',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            16 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressStreet',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            17 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressCity',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            18 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressState',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            19 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressPostalCode',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            20 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'addressCountry',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            21 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'emergencyContact',
               'type' => '?App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            22 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'guardians',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            23 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'medicalNotes',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            24 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'replaceGuardians',
               'type' => 'bool',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array<string, mixed>  $data
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'withGuardians',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  list<GuardianReference>  $guardians
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'guardians',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Domains\\Student\\Application\\DTOs',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'emergencycontact' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
              'guardianreference' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentNumberGenerator.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Generates a sequential, school-scoped student number in the format
 * "STU-{SCHOOL_PREFIX}-{SEQUENCE}". Because students are soft-deleted only,
 * the count-based sequence never re-uses a number within a school.
 */',
         'namespace' => 'App\\Domains\\Student\\Application\\Services',
         'uses' => 
        array (
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'PREFIX',
               'value' => '\'STU\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'generate',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'format',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'sequence',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Application\\Services\\StudentService.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Facade over the Student application actions. This is the only public entry
 * point the presentation layer should use.
 */',
         'namespace' => 'App\\Domains\\Student\\Application\\Services',
         'uses' => 
        array (
          'archivestudentaction' => 'App\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction',
          'createstudentaction' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
          'graduatestudentaction' => 'App\\Domains\\Student\\Application\\Actions\\GraduateStudentAction',
          'reactivatestudentaction' => 'App\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction',
          'restorestudentaction' => 'App\\Domains\\Student\\Application\\Actions\\RestoreStudentAction',
          'suspendstudentaction' => 'App\\Domains\\Student\\Application\\Actions\\SuspendStudentAction',
          'transferstudentaction' => 'App\\Domains\\Student\\Application\\Actions\\TransferStudentAction',
          'updatestudentaction' => 'App\\Domains\\Student\\Application\\Actions\\UpdateStudentAction',
          'withdrawstudentaction' => 'App\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction',
          'createstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
          'transferstudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
          'updatestudentdata' => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'user' => 'App\\Models\\User',
          'lengthawarepaginator' => 'Illuminate\\Contracts\\Pagination\\LengthAwarePaginator',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'createStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\CreateStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'updateStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\UpdateStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'transferStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\TransferStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'suspendStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\SuspendStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'graduateStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\GraduateStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            5 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'withdrawStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\WithdrawStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            6 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reactivateStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\ReactivateStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            7 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'archiveStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\ArchiveStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            8 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'restoreStudent',
               'type' => 'App\\Domains\\Student\\Application\\Actions\\RestoreStudentAction',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\Domains\\Student\\Application\\DTOs\\CreateStudentData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\Domains\\Student\\Application\\DTOs\\UpdateStudentData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'transfer',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\Domains\\Student\\Application\\DTOs\\TransferStudentData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'suspend',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'until',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'graduate',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'withdraw',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'reactivate',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'archive',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'restore',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'paginate',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\Pagination\\LengthAwarePaginator',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'filters',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'perPage',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\Gender.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
       'scalarType' => 'string',
       'phpDoc' => NULL,
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Male',
           'value' => '\'male\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Female',
           'value' => '\'female\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Other',
           'value' => '\'other\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\StudentStatus.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
       'scalarType' => 'string',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Lifecycle status of a student record.
 *
 * Transitions are enforced by the domain actions:
 * active → suspended → active (reactivated)
 * active → withdrawn
 * active → graduated (terminal)
 * {any} → archived (terminal; archived records are immutable)
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Enums',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Active',
           'value' => '\'active\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Inactive',
           'value' => '\'inactive\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Suspended',
           'value' => '\'suspended\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Withdrawn',
           'value' => '\'withdrawn\'',
           'phpDoc' => NULL,
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Graduated',
           'value' => '\'graduated\'',
           'phpDoc' => NULL,
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Archived',
           'value' => '\'archived\'',
           'phpDoc' => NULL,
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'color',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isImmutable',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * A status from which no further modification is allowed.
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Enums',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isActive',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Whether the record is still considered an enrolled, active student.
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Enums',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Enums\\TransferStatus.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
       'scalarType' => 'string',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Admission-side transfer status of a student record. This is an independent
 * dimension from the lifecycle status: an incoming student is still "active",
 * an outgoing one keeps its lifecycle status while the transfer is recorded.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Enums',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'None',
           'value' => '\'none\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Incoming',
           'value' => '\'incoming\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Outgoing',
           'value' => '\'outgoing\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'color',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isActive',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentArchived.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentCreated.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentGraduated.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentReactivated.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentRestored.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentSuspended.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentTransferred.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentUpdated.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Events\\StudentWithdrawn.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
        1 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'student',
               'type' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'actor',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 65,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Exceptions\\AcademicAssignmentException',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Raised when the requested academic assignment (academic year / class) is not
 * available for the school the student belongs to, or is no longer active.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
         'uses' => 
        array (
          'runtimeexception' => 'RuntimeException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'RuntimeException',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'missingYear',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'inactiveYear',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'yearName',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'classMismatch',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'classId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Exceptions\\DuplicateStudentException',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Raised when a student number or email is already taken within the same
 * organization (school). Mirrors the SPR-002 duplicate resource convention.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
         'uses' => 
        array (
          'runtimeexception' => 'RuntimeException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'RuntimeException',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'studentNumber',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'number',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'email',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'email',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'message',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Exceptions\\InvalidStudentStateException',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Raised when an action is attempted on a student whose current status makes
 * it invalid (e.g. graduating an archived student).
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Exceptions',
         'uses' => 
        array (
          'runtimeexception' => 'RuntimeException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'RuntimeException',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'for',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'action',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'currentStatus',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\RuleUnique.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Rules\\RuleUnique',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * School-scoped uniqueness rules for student identifiers.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
         'uses' => 
        array (
          'rule' => 'Illuminate\\Validation\\Rule',
          'unique' => 'Illuminate\\Validation\\Rules\\Unique',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'studentNumber',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  int|string|null  $ignoreId
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
             'uses' => 
            array (
              'rule' => 'Illuminate\\Validation\\Rule',
              'unique' => 'Illuminate\\Validation\\Rules\\Unique',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Illuminate\\Validation\\Rules\\Unique',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'ignoreId',
               'type' => 'int|string|null|null',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'email',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  int|string|null  $ignoreId
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
             'uses' => 
            array (
              'rule' => 'Illuminate\\Validation\\Rule',
              'unique' => 'Illuminate\\Validation\\Rules\\Unique',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Illuminate\\Validation\\Rules\\Unique',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'ignoreId',
               'type' => 'int|string|null|null',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'admissionNumber',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  int|string|null  $ignoreId
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
             'uses' => 
            array (
              'rule' => 'Illuminate\\Validation\\Rule',
              'unique' => 'Illuminate\\Validation\\Rules\\Unique',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Illuminate\\Validation\\Rules\\Unique',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'ignoreId',
               'type' => 'int|string|null|null',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Rules\\StudentRules.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Rules\\StudentRules',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Validation vocabulary for the Student aggregate. Rules are declared once here
 * and reused by the Filament forms and by the application actions\' pre-checks.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
         'uses' => 
        array (
          'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
          'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
          'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  int|string|null  $ignoreStudentId
     * @return array<string, array<int, mixed>>
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'ignoreStudentId',
               'type' => 'int|string|null|null',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  int|string  $ignoreStudentId
     * @return array<string, array<int, mixed>>
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\Rules',
             'uses' => 
            array (
              'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
              'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
              'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schoolId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'ignoreStudentId',
               'type' => 'int|string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Authorization for student lifecycle operations. Extends the SPR-002 school
 * resource pattern with the domain-specific lifecycle permissions
 * (transfer / suspend / graduate / archive / restore).
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\Traits',
         'uses' => 
        array (
          'user' => 'App\\Models\\User',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
        ),
         'constUses' => 
        array (
        ),
      )),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'permissionGroup',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => true,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'viewAny',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'restore',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'forceDelete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'transfer',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'suspend',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'graduate',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        11 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'archive',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Address.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\Address',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'street',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'city',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'state',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'postalCode',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'country',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromParts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'street',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'city',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'state',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'postalCode',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'country',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isComplete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'formatted',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\AdmissionReference',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Reference to the admission that placed this student in the school.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
         'uses' => 
        array (
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'MAX_LENGTH',
               'value' => '100',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'value',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\BirthDate.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\BirthDate',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'from',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'date',
               'type' => 'DateTimeImmutable|string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'age',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toDateString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmailAddress',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'MAX_LENGTH',
               'value' => '255',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'value',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\EmergencyContact',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'name',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'phone',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'relationship',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'name',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'phone',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'relationship',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromNullable',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => '?self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'name',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'phone',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'relationship',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\GuardianReference',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * A self-contained reference to a student\'s guardian. Guardians may later be
 * upgraded to first-class entities; the value object keeps the domain stable
 * in the meantime.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
         'uses' => 
        array (
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'RELATIONSHIPS',
               'value' => '[\'parent\', \'guardian\', \'sibling\', \'other\']',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'name',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'phone',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'email',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'relationship',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'isPrimary',
          ),
           'phpDoc' => NULL,
           'type' => 'bool',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'name',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'phone',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'email',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'relationship',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'isPrimary',
               'type' => 'bool',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'display',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromNullableGuardian',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Builds a reference from a raw form array, or null when the row is empty.
     *
     * @param  array<string, mixed>  $data
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
             'uses' => 
            array (
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => '?self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Nationality.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\Nationality',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'MAX_LENGTH',
               'value' => '100',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'value',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\PhoneNumber',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'value',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\Photo.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\Photo',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Value object describing a student photo. Only stores the reference/URL; the
 * binary file itself is managed by the storage layer.
 */',
         'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'from',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'path',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'empty',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isSet',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'value',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentName.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentName',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'firstName',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'lastName',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'middleName',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'preferredName',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'firstName',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'lastName',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'middleName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'preferredName',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fullName',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'displayName',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The name a student prefers to be addressed by.
     */',
             'namespace' => 'App\\Domains\\Student\\Domain\\ValueObjects',
             'uses' => 
            array (
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Domain\\ValueObjects\\StudentNumber',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'MAX_LENGTH',
               'value' => '50',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'value',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'equals',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'other',
               'type' => 'self',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentCreated',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentUpdated',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentTransferred',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentGraduated',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentSuspended',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentReactivated',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentWithdrawn',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentArchived',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleStudentRestored',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\Student.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Aggregate root of the Student domain (SPR-003).
 *
 * Invariants are enforced by the application actions, never by the model
 * itself: a unique student number per school, an optional but unique email,
 * a non-future birth date, an admission date inside the assigned academic
 * year, and a single current academic assignment.
 */',
         'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
         'uses' => 
        array (
          'academiccalendaryear' => 'App\\Domains\\Academic\\Infrastructure\\Models\\AcademicCalendarYear',
          'classsection' => 'App\\Domains\\Academic\\Infrastructure\\Models\\ClassSection',
          'schoolclass' => 'App\\Domains\\Academic\\Infrastructure\\Models\\SchoolClass',
          'school' => 'App\\Domains\\School\\Models\\School',
          'gender' => 'App\\Domains\\Student\\Domain\\Enums\\Gender',
          'studentstatus' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus',
          'transferstatus' => 'App\\Domains\\Student\\Domain\\Enums\\TransferStatus',
          'user' => 'App\\Models\\User',
          'hasschoolid' => 'App\\Traits\\HasSchoolId',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'App\\Traits\\HasSchoolId',
        2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'newFactory',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getFullNameAttribute',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'school',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'academicCalendarYear',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'schoolClass',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'section',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'guardians',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scopeActive',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'query',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        11 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scopeArchived',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'query',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        12 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scopeByStatus',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'query',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'status',
               'type' => 'App\\Domains\\Student\\Domain\\Enums\\StudentStatus|string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        13 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isActive',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        14 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isArchived',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        15 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isGraduated',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        16 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isSuspended',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        17 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isWithdrawn',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        18 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isInactive',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        19 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isImmutable',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        20 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'displayName',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Pivot between a student and an embedded guardian reference. Kept future-ready:
 * a `guardian_id` may later link to a first-class Guardian entity.
 */',
         'namespace' => 'App\\Domains\\Student\\Infrastructure\\Models',
         'uses' => 
        array (
          'hasschoolid' => 'App\\Traits\\HasSchoolId',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'App\\Traits\\HasSchoolId',
        2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'student',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Generates the public ULID `uuid` for every Student-domain model. Mirrors the
 * SPR-002 AcademicEntityObserver convention.
 */',
         'namespace' => 'App\\Domains\\Student\\Infrastructure\\Observers',
         'uses' => 
        array (
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'creating',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Domains\\Student\\Domain\\Traits\\HandlesStudentAuthorization',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'permissionGroup',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Filament\\Resources\\Resource',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Traits\\ScopedByTenant',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'model',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'navigationIcon',
          ),
           'phpDoc' => NULL,
           'type' => 'string|BackedEnum|null',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'navigationGroup',
          ),
           'phpDoc' => NULL,
           'type' => 'string|UnitEnum|null',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'navigationLabel',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'modelLabel',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'pluralModelLabel',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'recordTitleAttribute',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'navigationSort',
          ),
           'phpDoc' => NULL,
           'type' => '?int',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'form',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Filament\\Schemas\\Schema',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schema',
               'type' => 'Filament\\Schemas\\Schema',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'table',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Filament\\Tables\\Table',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'table',
               'type' => 'Filament\\Tables\\Table',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getRelations',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        11 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getPages',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        12 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'canDelete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'record',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        13 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'canDeleteAny',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\CreateStudent',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Filament\\Resources\\Pages\\CreateRecord',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'resource',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'title',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleRecordCreation',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Model',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getRedirectUrl',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\EditStudent',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Filament\\Resources\\Pages\\EditRecord',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'resource',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'title',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handleRecordUpdate',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Model',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'record',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'mutateFormDataBeforeFill',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getHeaderActions',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getRedirectUrl',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\ListStudents.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\Pages\\ListStudents',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Filament\\Resources\\Pages\\ListRecords',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'resource',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'title',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getHeaderActions',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\RelationManagers\\GuardiansRelationManager.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\Presentation\\Filament\\Resources\\StudentResource\\RelationManagers\\GuardiansRelationManager',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Filament\\Resources\\RelationManagers\\RelationManager',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'relationship',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'title',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'recordTitleAttribute',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'form',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Filament\\Schemas\\Schema',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schema',
               'type' => 'Filament\\Schemas\\Schema',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'table',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Filament\\Tables\\Table',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'table',
               'type' => 'Filament\\Tables\\Table',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\xampp\\htdocs\\NexusSchool\\app\\Domains\\Student\\StudentServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Domains\\Student\\StudentServiceProvider',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Owning service provider for the Student domain (SPR-003). Registers the
 * domain\'s bindings, observers, policies and event listeners; follows the
 * SPR-002 decision D4 of keeping the legacy EventServiceProvider untouched.
 */',
         'namespace' => 'App\\Domains\\Student',
         'uses' => 
        array (
          'studentnumbergenerator' => 'App\\Domains\\Student\\Application\\Services\\StudentNumberGenerator',
          'studentservice' => 'App\\Domains\\Student\\Application\\Services\\StudentService',
          'studentarchived' => 'App\\Domains\\Student\\Domain\\Events\\StudentArchived',
          'studentcreated' => 'App\\Domains\\Student\\Domain\\Events\\StudentCreated',
          'studentgraduated' => 'App\\Domains\\Student\\Domain\\Events\\StudentGraduated',
          'studentreactivated' => 'App\\Domains\\Student\\Domain\\Events\\StudentReactivated',
          'studentrestored' => 'App\\Domains\\Student\\Domain\\Events\\StudentRestored',
          'studentsuspended' => 'App\\Domains\\Student\\Domain\\Events\\StudentSuspended',
          'studenttransferred' => 'App\\Domains\\Student\\Domain\\Events\\StudentTransferred',
          'studentupdated' => 'App\\Domains\\Student\\Domain\\Events\\StudentUpdated',
          'studentwithdrawn' => 'App\\Domains\\Student\\Domain\\Events\\StudentWithdrawn',
          'studentauditlogger' => 'App\\Domains\\Student\\Infrastructure\\Listeners\\StudentAuditLogger',
          'student' => 'App\\Domains\\Student\\Infrastructure\\Models\\Student',
          'studentguardian' => 'App\\Domains\\Student\\Infrastructure\\Models\\StudentGuardian',
          'studententityobserver' => 'App\\Domains\\Student\\Infrastructure\\Observers\\StudentEntityObserver',
          'studentpolicy' => 'App\\Domains\\Student\\Infrastructure\\Policies\\StudentPolicy',
          'event' => 'Illuminate\\Support\\Facades\\Event',
          'gate' => 'Illuminate\\Support\\Facades\\Gate',
          'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
