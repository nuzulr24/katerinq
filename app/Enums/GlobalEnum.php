<?php

namespace App\Enums;

use MadWeb\Enum\Enum;

final class GlobalEnum extends Enum
{
    // enum management menu
    const isAdmin = 1;
    const isEditor = 2;
    const isModerator = 3;
    const isMembers = 4;

    // enum sites status
    const isWebsiteActive = 1;
    const isWebsiteInReview = 2;
    const isWebsiteNotActive = 3;
    const isWebsiteRejected = 4;
    const isWebsiteDeactivated = 5;

    // enum domain status
    const isDomainActive = 1;
    const isDomainInReview = 2;
    const isDomainNotActive = 3;
    const isDomainRejected = 4;
    const isDomainDeactivated = 5;

    // enum log type
    const LogOfLogin = 1;
    const LogOfGeneral = 2;
    
    // enum buzzer services status
    const isServiceActive = 1;
    const isServiceNotActive = 2;
    
    // enum withdrawal status
    const isWithdrawPending = 1;
    const isWithdrawOnProgress = 2;
    const isWithdrawPaid = 3;
    const isWithdrawCancel = 4;

    // enum order status
    const isOrderRequested = 1;
    const isOrderOnWorking = 2;
    const isOrderSubmitted = 3;
    const isOrderRevision = 4;
    const isOrderCompleted = 5;
    const isOrderReqCancel = 6;
    const isOrderCancelled = 7;
    const isOrderRejected = 8;

    // enum order history status
    const isHistoryDone = 1;
    const isHistoryRevision = 2;
    const isHistoryJobDone = 3;
    const isHistoryReqCancel = 4;
    const isHistoryCanceled = 5;

    // enum product buzzer & services status
    const isProductActive = 1;
    const isProductNotActive = 2;
    
    // enum product buzzer order status
    const isProductOrderPending = 1;
    const isProductOrderPaymentAlready = 2;
    const isProductOrderProcess = 3;
    const isProductOrderDone = 4;
    const isProductOrderCancel = 5;
    
    const isProductOrderTypeMixed = 1;
    const isProductOrderTypeMen = 2;
    const isProductOrderTypeWomen = 3;
    
    // enum product buzzer order detail comment status
    const isProductCommentPending = 1;
    const isProductCommentInReview = 2;
    const isProductCommentDeclined = 3;
    const isProductCommentApproved = 4;

    // enum order type
    const isOrderSites = 1;
    const isOrderDomain = 2;

    // enum user status
    const isActive = 1;
    const isInactive = 2;
    const isDeactive = 3;
    const isNotVerified = 4;

    // enum seller status
    const isSellerActive = 1;
    const isSellerInActive = 2;

    // enum order status
    const isOrderPending = 1;
    const isOrderProcessing = 2;
    const isOrderInReview = 3;
    const isOrderDeclined = 4;
    const isOrderSuccessed = 5;

    // enum deposit status
    const isDepositPending = 1;
    const isDepositInquiry = 2;
    const isDepositFailed = 3;
    const isDepositPaid = 4;
    const isDepositCancel = 5;
    
    // enum notifier status
    const isNotifyAsSeller = 2;
    const isNotifyAsUser = 1;

    // enum methodWithDeposit
    const isMethodPayLater = 0;
    const isMethodVirtual = 1;

    // enum promotion status
    const isPromotionAvailable = 1;
    const isPromotionUsed = 2;

    // enum content status
    const isPostPublished = 1;
    const isPostDraft = 2;

    // enum ticket status
    const isTicketPending = 1;
    const isTicketClosed = 2;
    const isTicketReplied = 3;

    /* enum website-config
        [1] SMTP Config
        [2] Maintenance Mode
    */
    const isMailerEnabled = 1;
    const isMailerDisabled = 2;
    const isMaintenanceMode = 3;

    /* enum for seller modules
        [1] Sites Listing
        [2] Rekening
    */
    const isSiteActive = 1;
    const isSiteInReview = 2;
    const isSiteNotActive = 3;
    const isSiteRejected = 4;
    const isSiteDeactivated = 5;

    const isSiteOwner = 1;
    const isSiteAuthor = 2;

    const isSiteTypeDoFollow = 1;
    const isSiteTypeNoFollow = 2;

    // rekening status
    const isRekeningActive = 1;
    const isRekeningInactive = 2;

    // ticket status
    const isTicketSellerPending = 1;
    const isTicketUserReplied = 2;
    const isTicketAdminReplied = 3;
    const isTicketSellerClosed = 4;

    const isTicketPriorityNormal = 1;
    const isTicketPriorityMedium = 2;
    const isTicketPriorityHigh = 3;
}
