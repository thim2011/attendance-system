<!-- Modal 通知 -->
<div class="modal fade" id="modal-message" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">提醒</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <p id="modal-message-text"></p>
            </div>
        </div>
    </div>
</div>

<!--  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div> -->
<!-- Modal 成功 -->
<div class="modal fade" id="modal-success" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title fw-bold">成功</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <p id="modal-message-success"></p>
            </div>
        </div>
    </div>
</div>


<!-- Moda 決定 -->
<div class="modal fade" id="CheckModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">審核確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                您確定了嗎？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="confirmAccept" data-action="" data-holiday-id=""
                    data-employee-id="" data-toggle="">確定</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 新增假日 -->
<div class="modal fade" id="modal-holiday" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">新增假日</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="holiday-form">
                    <div class="mb-3">
                        <label for="holiday-date" class="col-form-label"><span class="text-danger">*</span>日期:</label>
                        <input type="date" class="form-control" name="holiday-date">
                    </div>
                    <div class="mb-3">
                        <label for="holiday-name" class="col-form-label"><span class="text-danger">*</span>名字：</label>
                        <input type="text" class="form-control" name="holiday-name">
                    </div>
                    <div class="mb-3">
                        <label for="is_holiday" class="col-form-label"><span
                                class="text-danger">*</span>是否放假：</label><br>
                        <select class="selectcustom p-2 width100" name="is_holiday" id="is_holiday">
                            <option value="true">是</option>
                            <option value="false">否</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="col-form-label">類別：</label>
                        <input type="text" class="form-control" name="category">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="col-form-label">描述：</label>
                        <input type="text" class="form-control" name="description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="PostHoliday">新增</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 權限 -->
<div class="modal fade" id="modal-permissions" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-light">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">更改權限</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permissions-form">
                    <select class="selectcustom p-2 width100" name="permissions-select" id="permissions-select">
                        <option value="2">使用者</option>
                        <option value="1">管理者</option>
                    </select>
                    <input type="hidden" name="employID_hide" id="employID_hide" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="comfirm-Permissions">確認</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 補打卡 -->
<div class="modal fade" id="modal-ForgotPunchin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">申請忘記打卡</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forgot-punchin-form">
                    <div class="mb-3">
                        <label for="forgot-punchin-type" class="col-form-label">上班:</label>
                        <select class="selectcustom p-2 width100" name="forgot-punchin-type">
                            <option value="">請選擇</option>
                            <option value="punchin">上班</option>
                            <option value="punchout">下班</option>
                        </select>
                    </div>
                    <span class="text-danger" id="punchin-type-notice"></span>
                    <div class="mb-3">
                        <label for="forgot-punchin-date" class="col-form-label">日期:</label>
                        <input type="date" class="form-control" name="forgot-punchin-date">
                    </div>
                    <span class="text-danger" id="punchin-date-notice"></span>
                    <div class="mb-3">
                        <label for="forgot-punchin-time" class="col-form-label">時間:</label>
                        <input type="time" class="form-control" name="forgot-punchin-time">
                    </div>
                    <span class="text-danger" id="punchin-time-notice"></span>
                    <input type="hidden" name="employee_id" value="<?php echo $userId ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                <button type="button" class="btn btn-primary" id="forgot-punchin-comfirm">確認送出</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 拒絕請假理由 -->
<div class="modal fade" id="modal-RejectReason" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-light fw-bold">拒絕請假</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="m-2">
                    <label for="Reject_Reason">拒絕理由：</label>
                    <textarea type="text" class="inputcustom" id="Reject_Reason" rows="4" cols="50"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                <button type="button" class="btn btn-primary" id="Reject-Btn">確認送出</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 修改打卡時間 -->
<div class="modal fade" id="modal-Punchtime" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light fw-bold">打卡時間修改</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPunchForm" class="m-2">
                    <label>日期：<span id="date_punch"></span></label><br>
                    <label for="Punchin_time">上班時間：</label>
                    <input type="time" class="inputcustom" name="Punchin_time" id="Punchin_time" step="1">
                    <label for="Punchout_time">下班時間：</label>
                    <input type="time" class="inputcustom" name="Punchout_time" id="Punchout_time" step="1">
                    <input type="hidden" id="Attendance_id" name="Attendance_id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                <button type="button" class="btn btn-primary" id="Edit_punchtime">確認送出</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal 確認刪除 -->
<div class="modal fade" id="modal-DeletePunchtime" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-light fw-bold">確定要刪除</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="model-text"></p>
                <input type="hidden" id="delete_Attendance_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                <button type="button" class="btn btn-danger" id="comfirm_delete">確認送出</button>
            </div>
        </div>
    </div>
</div>